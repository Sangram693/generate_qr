<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Requests\StorePageRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;
use TCPDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PageController extends Controller
{
    public function store(StorePageRequest $request)
    {
        $user = auth()->user();
        $request->merge(['user_id' => $user->id]);

        $excelFile = $request->file('excel_file');

        if (!$excelFile->isValid() || !in_array($excelFile->getClientOriginalExtension(), ['xlsx', 'xls'])) {
            return response()->json(['message' => 'Excel file must be of type xlsx or xls'], 400);
        }

        // Store file in storage/app/public/excel_files
        $fileName = time() . '_' . $excelFile->getClientOriginalName();
        $filePath = $excelFile->storeAs('excel_files', $fileName, 'public');

        // Store page details in database
        $page = Page::create([
            'page_height' => $request->page_height,
            'page_width' => $request->page_width,
            'margin_top' => $request->margin_top,
            'margin_bottom' => $request->margin_bottom,
            'margin_left' => $request->margin_left,
            'margin_right' => $request->margin_right,
            'qr_height' => $request->qr_height,
            'qr_width' => $request->qr_width,
            'excel_file' => $filePath,
            'user_id' => $request->user_id,
        ]);

        // Load Excel file from storage
        $absoluteFilePath = storage_path("app/public/" . $filePath);
        if (!file_exists($absoluteFilePath)) {
            return response()->json(['message' => 'Uploaded file not found.'], 400);
        }
        
        $spreadsheet = IOFactory::load($absoluteFilePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];

        foreach ($worksheet->getRowIterator() as $row) {
            if ($row->getRowIndex() == 1) { // Skip the first row (headers)
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true); // Ignore empty cells

            $firstCell = $cellIterator->current(); // Get the first column (A)
            if ($firstCell) {
                $value = trim($firstCell->getValue()); // Ensure no spaces
                if (!empty($value)) {
                    $data[] = $value; // Store only column A values as strings
                }
            }
        }

        $page_width = $request->page_width;
        $page_height = $request->page_height;
        $qr_width = $request->qr_width;
        $qr_height = $request->qr_height;
        $margin_top = $request->margin_top;
        $margin_bottom = $request->margin_bottom;
        $margin_left = $request->margin_left;
        $margin_right = $request->margin_right;

        // Create TCPDF instance
        $pdf = new TCPDF('P', 'mm', [$page_width, $page_height], true, 'UTF-8', false);
        $pdf->setPrintHeader(false); // Disable header
        $pdf->setPrintFooter(false); // Disable footer if needed
        $pdf->SetCreator('Laravel');
        $pdf->SetAuthor($user->name);
        $pdf->SetTitle('QR Code PDF');
        $pdf->SetMargins($margin_left, $margin_top, $margin_right);
        $pdf->SetAutoPageBreak(TRUE, $margin_bottom);
        $pdf->AddPage();

        // QR Code Positioning Variables
        $x = $margin_left;
        $y = $margin_top;
        $columns = 16; // Number of QR codes in a row
        $rows = 27; // Number of QR codes in a column
        $qr_per_page = $columns * $rows;
        $count = 0;

        foreach ($data as $item) {
            if ($count > 0 && $count % $qr_per_page == 0) {
                $pdf->AddPage();
                $x = $margin_left;
                $y = $margin_top;
            }
        
            // Generate QR Code in SVG format
            $qrCodeSvg = QrCode::format('svg')->style('dot')->size($qr_width)->generate($item);
            
            // Convert the SVG to a TCPDF-compatible format
            $tempPath = storage_path('app/public/temp_qr_codes/');
if (!file_exists($tempPath)) {
    mkdir($tempPath, 0777, true); // Create directory if it doesn't exist
}
$tempFile = $tempPath . 'temp_qr_' . $count . '.svg';
file_put_contents($tempFile, $qrCodeSvg);

        
            // Add QR Code to PDF
            $pdf->ImageSVG($file=$tempFile, $x, $y, $qr_width, $qr_height);
        
            // Update position for next QR Code
            $x += $qr_width + $margin_left + $margin_right; // Adjust spacing
        
            if ($count % $columns == $columns - 1) { // Move to next row after every full row
                $x = $margin_left;
                $y += $qr_height + $margin_top + $margin_bottom;
            }
        
            $count++;
        }

        return $pdf->Output('qrcodes.pdf', 'D');
    }
}
