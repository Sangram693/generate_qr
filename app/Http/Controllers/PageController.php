<?php

namespace App\Http\Controllers;

use TCPDF;
use App\Models\Beam;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Requests\StorePageRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PageController extends Controller
{

    // public function index()
    // {
    //     return Page::all()->makeHidden('user_id')->load('user');
    // }

    public function index()
{
    return Page::query()
        ->with('user') // Load user relation
        ->orderBy('created_at', 'desc') // Order before fetching
        ->get()
        ->makeHidden('user_id'); // Hide user_id after fetching
}

    public function store(StorePageRequest $request)
    {
        $user = auth()->user();
        $request->merge(['user_id' => $user->id]);

        // Get row number from request
        $rowNumber = $request->row_number;

        // Step 1: Generate Random Data & Save as Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Random Data');

        // Set Header
        $sheet->setCellValue('A1', 'id');
        $sheet->setCellValue('B1', 'model_no');
        $sheet->setCellValue('C1', 'bach_no');
        $sheet->setCellValue('D1', 'serial_no');
        $sheet->setCellValue('E1', 'mfd_origin');
        $sheet->setCellValue('F1', 'mfd_date');
        $sheet->setCellValue('G1', 'asp');
        $sheet->setCellValue('H1', 'status');
        

        // Generate 16-character ids for each row
        $data = [];
        for ($i = 2; $i <= $rowNumber + 1; $i++) {
            $randomValue = $this->generateRandomString(16);
            $sheet->setCellValue('A' . $i, $randomValue);
            $beam = new Beam();
            $beam->id = $randomValue;
            $beam->save();
            $data[] = $beam->id;
        }

        // **Fix: Proper Storage Path**
        $fileName = 'random_values_' . time() . '.xlsx';
        $filePath = 'excel_files/' . $fileName;
        $storagePath = storage_path('app/public/' . $filePath);
        $filePath = 'storage/' . $filePath;

        // **Fix: Ensure Directory Exists**
        if (!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0777, true);
        }

        // Save Excel file to storage
        $writer = new Xlsx($spreadsheet);
        $writer->save($storagePath);

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
            'excel_file' => $filePath, // Relative to public storage
            'user_id' => $request->user_id,
        ]);

        

        // Step 2: Generate QR Codes & Create PDF
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
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Laravel');
        $pdf->SetAuthor($user->name);
        $pdf->SetTitle('QR Code PDF');
        $pdf->SetMargins($margin_left, $margin_top, $margin_right);
        $pdf->SetAutoPageBreak(true, $margin_bottom);
        $pdf->AddPage();
        
        $logoPath = public_path('UT LOGO.png');
        if (!file_exists($logoPath)) {
            return response()->json(['error' => 'Logo file not found'], 500);
        }
        
        $border_width = 1;
        $qr_spacing = $margin_left;
        $qr_total_width = $qr_width + $border_width + $qr_spacing;
        $qr_total_height = $qr_height + $border_width + 11 + $qr_spacing;
        
        $columns = (int) (($page_width) / ($qr_total_width + $margin_left));
        $rows = (int) (($page_height) / ($qr_total_height  + $margin_top));
        
        $qr_per_page = $columns * $rows;
        $count = 0;
        $x = $margin_left;
        $y = $margin_top;
        
        $tempPath = storage_path('app/public/temp_qr_codes/');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }
        
        foreach ($data as $beamId) {
            $beam = Beam::find($beamId);
            if (!$beam) continue;
        
            // **Check if QR code is generated properly**
            $qrCodeSvg = QrCode::format('svg')->size($qr_width)->generate("www.utkarshsmart.in/api/w-beam/$beam->id");
            $tempFile = "{$tempPath}temp_qr_$count.svg";
            file_put_contents($tempFile, $qrCodeSvg);
        
            if (!file_exists($tempFile)) {
                return response()->json(['error' => "QR Code $count file not created"], 500);
            }
        
            // **Draw Only the Border Around QR Code (Blue)**
$pdf->SetDrawColor(56, 182, 255); // Border Color #38b6ff
$pdf->SetLineWidth(1); // Border Thickness
$logo_height = 8; // Fixed logo height
$padding = 1; 
$total_border_height = $qr_height + 13; // QR Height + Logo Height + Padding
$pdf->Rect($x - 1, $y - 1, $qr_width + 4, $total_border_height + 0.7, 'D'); // Full Border


$qr_x = $x + 1; // Align QR to left within border
$qr_y = $y + $padding; // Add top padding
$pdf->ImageSVG($tempFile, $qr_x, $qr_y, $qr_width, $qr_height);

$line_y = $qr_y + $qr_height + 2; // Position: QR bottom + 2px padding
$pdf->SetDrawColor(56, 182, 255); // Black color for line
$pdf->SetLineWidth(0.5); // Thin line
$pdf->Line($x -1, $line_y, $x + $qr_width + 3, $line_y);
// **Properly Centered Logo**
$svgContent = file_get_contents($logoPath);
$logo_width = $qr_width;
$logo_x = $x + 1 +($qr_width - $logo_width) / 2; // Center horizontally
$logo_y = $qr_y + $qr_height + $padding +2; // Position below QR with padding
$pdf->Image(public_path('ut logo up.png'), $logo_x, $logo_y, $logo_width, $logo_height);










        
            // **Position Update**
            $x += $qr_total_width;  
            $count++;
        
            if ($count % $columns == 0) {
                $x = $margin_left;
                $y += $qr_total_height;
            }
        
            if ($count % $qr_per_page == 0) {
                $pdf->AddPage();
                $x = $margin_left;
                $y = $margin_top;
            }
        
            unlink($tempFile);
        }
        
        $pdfPath = storage_path('app/public/beam_qrcodes.pdf');
        $pdf->Output($pdfPath, 'F');
        
        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'PDF file not generated'], 500);
        }
        
        return response()->download($pdfPath);
        
    }

    private function generateRandomString($length = 16)
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, $length);
    }
}
