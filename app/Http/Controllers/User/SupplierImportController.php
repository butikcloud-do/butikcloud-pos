<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SupplierImportController extends Controller
{
    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers matching the fields from user/supplier/list
        $headers = [
            'Company Name',
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
                'Tech Solutions Ltd.',
                'John Doe',
                'contact@techsolutions.com',
                '1234567890',
                '123 Tech Park',
                'San Francisco',
                'CA',
                '94016',
                '94016',
                'USA'
            ],
            [
                'Global logistics',
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

        // Set response headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="supplier_import_sample.xlsx"');
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

            // Check Limit
            $totalNewSuppliers = count($rows);
            // Assuming 'supplier_limit' is available on user object (standard in this system)
            if ($user->supplier_limit != -1 && $user->supplier_limit < $totalNewSuppliers) {
                 return jsonResponse('validation_error', 'error', ["You have reached your supplier limit. Remaining: {$user->supplier_limit}, Trying to import: {$totalNewSuppliers}"]);
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
                foreach ($validData as $supplierData) {
                    $this->createSupplierFromImport($supplierData, $user);
                    $importedCount++;
                }

                DB::commit();

                $message[] = "Successfully imported {$importedCount} suppliers";
                adminActivity("supplier-bulk-import", Supplier::class, 0, "Imported {$importedCount} suppliers successfully");
                return jsonResponse('import_success', 'success', $message);

            } catch (Exception $e) {
                DB::rollBack();
                $message[] = "Import failed: " . $e->getMessage();
                adminActivity("supplier-bulk-import", Supplier::class, 0, "Import failed: " . $e->getMessage());
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
            'company_name' => trim($row[0] ?? ''),
            'name'         => trim($row[1] ?? ''),
            'email'        => trim($row[2] ?? ''),
            'mobile'       => trim($row[3] ?? ''),
            'address'      => trim($row[4] ?? ''),
            'city'         => trim($row[5] ?? ''),
            'state'        => trim($row[6] ?? ''),
            'zip'          => trim($row[7] ?? ''),
            'postcode'     => trim($row[8] ?? ''),
            'country'      => trim($row[9] ?? ''),
        ];

        // Required field validation
        if (empty($data['company_name'])) {
            $errors[] = "Row {$rowNumber}: Company Name is required";
        } elseif (strlen($data['company_name']) > 255) {
            $errors[] = "Row {$rowNumber}: Company Name must not exceed 255 characters";
        }

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

        // Unique checks
        if (!empty($data['company_name'])) {
            $exists = Supplier::where('user_id', $user->id)
                ->where('company_name', $data['company_name'])
                ->exists();
            if ($exists) {
                $errors[] = "Row {$rowNumber}: Company Name '{$data['company_name']}' already exists";
            }
        }

        if (!empty($data['mobile'])) {
            $exists = Supplier::where('user_id', $user->id)
                ->where('mobile', $data['mobile'])
                ->exists();
            if ($exists) {
                $errors[] = "Row {$rowNumber}: Mobile '{$data['mobile']}' already exists";
            }
        }

        // Email validation (if provided)
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: Invalid email format";
            } elseif (strlen($data['email']) > 255) {
                $errors[] = "Row {$rowNumber}: Email must not exceed 255 characters";
            } else {
                // Check unique email for this user
                $exists = Supplier::where('user_id', $user->id)
                    ->where('email', $data['email'])
                    ->exists();
                if ($exists) {
                    $errors[] = "Row {$rowNumber}: Email '{$data['email']}' already exists";
                }
            }
        }

        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return [
            'valid' => true,
            'data'  => [
                'company_name' => $data['company_name'],
                'name'         => $data['name'],
                'email'        => $data['email'],
                'mobile'       => $data['mobile'],
                'address'      => $data['address'],
                'city'         => $data['city'],
                'state'        => $data['state'],
                'zip'          => $data['zip'],
                'postcode'     => $data['postcode'],
                'country'      => $data['country'],
                'row_number'   => $rowNumber,
            ]
        ];
    }

    private function createSupplierFromImport($data, $user)
    {
        $supplier               = new Supplier();
        $supplier->user_id      = $user->id;
        $supplier->company_name = $data['company_name'];
        $supplier->name         = $data['name'];
        $supplier->email        = $data['email'];
        $supplier->mobile       = $data['mobile'];
        $supplier->address      = $data['address'];
        $supplier->city         = $data['city'];
        $supplier->state        = $data['state'];
        $supplier->zip          = $data['zip'];
        $supplier->postcode     = $data['postcode'];
        $supplier->country      = $data['country'];

        $supplier->save();

        decrementFeature($user, 'supplier_limit');

        adminActivity("supplier-added", get_class($supplier), $supplier->id);
    }
}
