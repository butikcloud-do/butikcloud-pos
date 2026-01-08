<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BrandImportController extends Controller
{
    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Define headers matching the exact fields from user/brand/list
        $headers = [
            'Name'
        ];

        // Set headers
        $sheet->fromArray($headers, null, 'A1');

        // Add sample data rows
        $sampleData = [
            [
                'Samsung'
            ],
            [
                'Apple'
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'A') as $col) {
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
        $sheet->getStyle('A1:A1')->applyFromArray($headerStyle);

        // Set response headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="brand_import_sample.xlsx"');
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
                $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed

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
                foreach ($validData as $brandData) {
                    $this->createBrandFromImport($brandData, $user);
                    $importedCount++;
                }

                DB::commit();

                $message[] = "Successfully imported {$importedCount} brands";
                adminActivity("brand-bulk-import", Brand::class, 0, "Imported {$importedCount} brands successfully");
                return jsonResponse('import_success', 'success', $message);

            } catch (Exception $e) {
                DB::rollBack();
                $message[] = "Import failed: " . $e->getMessage();
                adminActivity("brand-bulk-import", Brand::class, 0, "Import failed: " . $e->getMessage());
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
            'name'      => trim($row[0] ?? ''),
        ];

        // Required field validation (Name)
        if (empty($data['name'])) {
            $errors[] = "Row {$rowNumber}: Name is required";
        } elseif (strlen($data['name']) > 40) {
            $errors[] = "Row {$rowNumber}: Name must not exceed 40 characters";
        }

        // Check if brand name already exists for this user
        if (!empty($data['name'])) {
            $existingBrand = Brand::where('user_id', $user->id)
                ->where('name', $data['name'])
                ->first();
            if ($existingBrand) {
                $errors[] = "Row {$rowNumber}: Brand Name '{$data['name']}' already exists";
            }
        }



        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }

        return [
            'valid' => true,
            'data'  => [
                'name'       => $data['name'],
                'row_number' => $rowNumber,
            ]
        ];
    }

    private function createBrandFromImport($data, $user)
    {
        $brand          = new Brand();
        $brand->user_id = $user->id;
        $brand->name    = $data['name'];
        $brand->status  = Status::ENABLE; 

        // ---------------------------------------------------

        $brand->save();
        adminActivity("brand-insert", get_class($brand), $brand->id);
    }
}
