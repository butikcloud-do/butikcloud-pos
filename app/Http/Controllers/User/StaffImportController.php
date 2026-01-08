<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StaffImportController extends Controller
{
    private $countryData = null;

    public function __construct()
    {
        // Load country data for lookup
        $path = resource_path('views/partials/country.json');
        if (file_exists($path)) {
            $this->countryData = json_decode(file_get_contents($path), true);
        }
    }

    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers matching the exact fields from New Staff page
        $headers = [
            'First Name',
            'Last Name',
            'Username',
            'Email Address',
            'Country',
            'Mobile',
            'City',
            'State',
            'Zip Code',
            'Address'
        ];

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data rows
        $sampleData = [
            [
                'John',
                'Doe',
                'johndoe123',
                'john@example.com',
                'United States',
                '1234567890',
                'New York',
                'NY',
                '10001',
                '123 Wall St'
            ],
            [
                'Jane',
                'Smith',
                'janesmith',
                'jane@example.com',
                'Canada',
                '9876543210',
                'Toronto',
                'ON',
                'M5V 2T6',
                '456 Bay St'
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Set response headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="staff_import_sample.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = getParentUser();

        // Check overall limit first (approximate check, refined later per row if needed, but atomic transaction means we need full capacity)
        // Wait, atomic or not, we should check if they HAVE limit for the whole batch.
        
        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                $message[] = "Excel file must contain at least a header row and one data row";
                return jsonResponse('validation_error', 'error', $message);
            }

            // Remove header row
            array_shift($rows);

            // Remove empty rows
            $rows = array_filter($rows, function ($row) {
                return !empty(array_filter($row));
            });

            if (empty($rows)) {
                $message[] = "No valid data rows found in the Excel file";
                return jsonResponse('validation_error', 'error', $message);
            }

            $totalNewStaff = count($rows);
            // Check limit
            if ($user->user_limit != -1 && $user->user_limit < $totalNewStaff) {
                 return jsonResponse('validation_error', 'error', ["You have reached your staff user limit. Remaining: {$user->user_limit}, Trying to import: {$totalNewStaff}"]);
            }

            $errors = [];
            $validData = [];

            // Validate each row
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                $validation = $this->validateRow($row, $rowNumber, $user);

                if ($validation['valid']) {
                    $validData[] = $validation['data'];
                } else {
                    $errors = array_merge($errors, $validation['errors']);
                }
            }

            if (!empty($errors)) {
                return jsonResponse('validation_error', 'error', $errors);
            }

            if (empty($validData)) {
                $message[] = "No valid data found to import";
                return jsonResponse('validation_error', 'error', $message);
            }

            // Start Transaction
            DB::beginTransaction();

            try {
                $importedCount = 0;
                foreach ($validData as $staffData) {
                    $this->createStaffFromImport($staffData, $user);
                    $importedCount++;
                }

                DB::commit();

                $message[] = "Successfully imported {$importedCount} new staff members. Welcome emails have been sent.";
                adminActivity("staff-bulk-import", User::class, 0, "Imported {$importedCount} staff successfully");
                return jsonResponse('import_success', 'success', $message);

            } catch (Exception $e) {
                DB::rollBack();
                $message[] = "Import failed: " . $e->getMessage();
                adminActivity("staff-bulk-import", User::class, 0, "Import failed: " . $e->getMessage());
                return jsonResponse('exception', 'error', $message);
            }

        } catch (Exception $e) {
            $message[] = "Failed to read Excel file: " . $e->getMessage();
            return jsonResponse('exception', 'error', $message);
        }
    }

    private function validateRow($row, $rowNumber, $user)
    {
        $errors = [];
        $countryInfo = null;

        $data = [
            'firstname' => trim($row[0] ?? ''),
            'lastname'  => trim($row[1] ?? ''),
            'username'  => trim($row[2] ?? ''),
            'email'     => trim($row[3] ?? ''),
            'country'   => trim($row[4] ?? ''),
            'mobile'    => trim($row[5] ?? ''),
            'city'      => trim($row[6] ?? ''),
            'state'     => trim($row[7] ?? ''),
            'zip'       => trim($row[8] ?? ''),
            'address'   => trim($row[9] ?? ''),
        ];

        // 1. Basic Required Checks
        if (empty($data['firstname'])) $errors[] = "Row {$rowNumber}: First Name is required";
        if (empty($data['lastname'])) $errors[] = "Row {$rowNumber}: Last Name is required";
        if (empty($data['username'])) $errors[] = "Row {$rowNumber}: Username is required";
        if (empty($data['email'])) $errors[] = "Row {$rowNumber}: Email is required";
        if (empty($data['country'])) $errors[] = "Row {$rowNumber}: Country is required";
        if (empty($data['mobile'])) $errors[] = "Row {$rowNumber}: Mobile is required";

        // 2. Resolve Country logic
        if (!empty($data['country'])) {
            $foundCountry = false;
            // $this->countryData structure: code => {country: "Name", dial_code: "1"}
            foreach ($this->countryData as $code => $info) {
                if (strtolower($info['country']) === strtolower($data['country'])) {
                    $countryInfo = $info;
                    $countryInfo['code'] = $code; // Store the code from the key
                    $foundCountry = true;
                    break;
                }
            }

            if (!$foundCountry) {
                 $errors[] = "Row {$rowNumber}: Invalid Country Name '{$data['country']}'";
            }
        }

        // 3. Username Unique Checks
        if (!empty($data['username'])) {
            if (strlen($data['username']) < 6) {
                $errors[] = "Row {$rowNumber}: Username must be at least 6 characters";
            } else {
                if (User::where('username', $data['username'])->exists()) {
                    $errors[] = "Row {$rowNumber}: Username '{$data['username']}' already exists";
                }
            }
        }

        // 4. Email Unique Checks
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: Invalid Email format";
            } else {
                if (User::where('email', $data['email'])->exists()) {
                     $errors[] = "Row {$rowNumber}: Email '{$data['email']}' already exists";
                }
            }
        }

        // 5. Mobile Unique Checks
        if (!empty($data['mobile']) && $countryInfo) {
            // Check regex
            if (!preg_match('/^([0-9]*)$/', $data['mobile'])) {
                 $errors[] = "Row {$rowNumber}: Mobile must contain only numbers";
            } else {
                // Check unique with dial code
                $exists = User::where('dial_code', $countryInfo['dial_code'])
                              ->where('mobile', $data['mobile'])
                              ->exists();
                if ($exists) {
                     $errors[] = "Row {$rowNumber}: Mobile number '{$data['mobile']}' already exists for country {$countryInfo['country']}";
                }
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        // Append resolved codes
        $data['country_code'] = $countryInfo['code']; // ISO code
        $data['mobile_code']  = $countryInfo['dial_code'];
        $data['country_name'] = $countryInfo['country']; // Normalized case
        $data['row_number']   = $rowNumber;

        return [
            'valid' => true,
            'data'  => $data
        ];
    }

    private function createStaffFromImport($data, $user)
    {
        $oneTimePassword = getNumber(10);

        $staff                   = new User();
        $staff->firstname        = $data['firstname'];
        $staff->lastname         = $data['lastname'];
        $staff->username         = $data['username'];
        $staff->email            = $data['email'];
        $staff->country_code     = $data['country_code']; // from lookup
        $staff->country_name     = $data['country_name'];
        $staff->dial_code        = $data['mobile_code'];
        $staff->mobile           = $data['mobile'];
        $staff->city             = $data['city'];
        $staff->state            = $data['state'];
        $staff->zip              = $data['zip'];
        $staff->address          = $data['address'];
        
        $staff->parent_id        = $user->id;
        $staff->password         = Hash::make($oneTimePassword);
        
        // Default verified status
        $staff->kv               = Status::KYC_VERIFIED;
        $staff->ev               = Status::VERIFIED;
        $staff->sv               = Status::VERIFIED;
        $staff->tv               = Status::VERIFIED;
        $staff->profile_complete = Status::YES;
        $staff->is_staff         = Status::YES;
        
        $staff->save();

        // Send Email with credentials
        notify($staff, 'STAFF_REGISTERED', [
            'user'        => $staff->fullname,
            'parent_user' => $user->username,
            'username'    => $staff->username,
            'email'       => $staff->email,
            'password'    => $oneTimePassword,
            'login_url'   => route('user.login'),
        ]);

        decrementFeature($user, 'user_limit');

        adminActivity("staff-added", get_class($staff), $staff->id);
    }
}
