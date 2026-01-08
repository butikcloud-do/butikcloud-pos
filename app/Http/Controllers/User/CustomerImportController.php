<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerImportController extends Controller
{
    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers matching the fields from user/customer/list
        $headers = [
            'Name',
            'Email',
            'Mobile',
            'Address',
            'City',
            'State',
            'Zip',
            'Postcode',
            'Country'
        ];

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data rows
        $sampleData = [
            [
                'John Doe',
                'john@example.com',
                '1234567890',
                '123 Main St',
                'New York',
                'NY',
                '10001',
                '10001',
                'USA'
            ],
            [
                'Jane Smith',
                '',
                '0987654321',
                '',
                '',
                '',
                '',
                '',
                ''
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
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
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Set response headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="customer_import_sample.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $user = getParentUser();

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

            $errors = [];
            $validData = [];

            // Validate each row BEFORE starting import
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header

                $validation = $this->validateRow($row, $rowNumber, $user);

                if ($validation['valid']) {
                    $validData[] = $validation['data'];
                } else {
                    $errors = array_merge($errors, $validation['errors']);
                }
            }

            // If ANY errors exist, stop here (no partial imports)
            if (!empty($errors)) {
                return jsonResponse('validation_error', 'error', $errors);
            }

            if (empty($validData)) {
                $message[] = "No valid data found to import";
                return jsonResponse('validation_error', 'error', $message);
            }

            // ALL-OR-NOTHING: Start transaction for atomic import
            DB::beginTransaction();

            try {
                $importedCount = 0;
                foreach ($validData as $customerData) {
                    $this->createCustomerFromImport($customerData, $user);
                    $importedCount++;
                }

                DB::commit();

                $message[] = "Successfully imported {$importedCount} customers";
                adminActivity("customer-bulk-import", Customer::class, 0, "Imported {$importedCount} customers successfully");
                return jsonResponse('import_success', 'success', $message);

            } catch (Exception $e) {
                DB::rollBack();
                $message[] = "Import failed: " . $e->getMessage();
                adminActivity("customer-bulk-import", Customer::class, 0, "Import failed: " . $e->getMessage());
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

        // Map Excel columns
        $data = [
            'name'     => trim($row[0] ?? ''),
            'email'    => trim($row[1] ?? ''),
            'mobile'   => trim($row[2] ?? ''),
            'address'  => trim($row[3] ?? ''),
            'city'     => trim($row[4] ?? ''),
            'state'    => trim($row[5] ?? ''),
            'zip'      => trim($row[6] ?? ''),
            'postcode' => trim($row[7] ?? ''),
            'country'  => trim($row[8] ?? ''),
        ];

        // Required field validation
        if (empty($data['name'])) {
            $errors[] = "Row {$rowNumber}: Name is required";
        } elseif (strlen($data['name']) > 255) {
            $errors[] = "Row {$rowNumber}: Name must not exceed 255 characters";
        }

        if (empty($data['mobile'])) {
            $errors[] = "Row {$rowNumber}: Mobile is required";
        } elseif (strlen($data['mobile']) > 255) {
            $errors[] = "Row {$rowNumber}: Mobile must not exceed 255 characters";
        }

        // Email validation (if provided)
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: Invalid email format";
            } elseif (strlen($data['email']) > 255) {
                $errors[] = "Row {$rowNumber}: Email must not exceed 255 characters";
            } else {
                // Check unique email for this user
                $existingEmail = Customer::where('user_id', $user->id)
                    ->where('email', $data['email'])
                    ->exists();
                if ($existingEmail) {
                    $errors[] = "Row {$rowNumber}: Email '{$data['email']}' already exists";
                }
            }
        }

        // Check unique mobile for this user
        if (!empty($data['mobile'])) {
            $existingMobile = Customer::where('user_id', $user->id)
                ->where('mobile', $data['mobile'])
                ->exists();
            if ($existingMobile) {
                $errors[] = "Row {$rowNumber}: Mobile '{$data['mobile']}' already exists";
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return [
            'valid' => true,
            'data'  => [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'mobile'   => $data['mobile'],
                'address'  => $data['address'],
                'city'     => $data['city'],
                'state'    => $data['state'],
                'zip'      => $data['zip'],
                'postcode' => $data['postcode'],
                'country'  => $data['country'],
                'row_number' => $rowNumber,
            ]
        ];
    }

    private function createCustomerFromImport($data, $user)
    {
        $customer           = new Customer();
        $customer->user_id  = $user->id;
        $customer->name     = $data['name'];
        $customer->email    = $data['email'];
        $customer->mobile   = $data['mobile'];
        $customer->address  = $data['address'];
        $customer->city     = $data['city'];
        $customer->state    = $data['state'];
        $customer->zip      = $data['zip'];
        $customer->postcode = $data['postcode'];
        $customer->country  = $data['country'];
        // $customer->status   = Status::ENABLE; // Assuming default is enabled or handled by DB default

        $customer->save();
        adminActivity("customer-added", get_class($customer), $customer->id);
    }
}
