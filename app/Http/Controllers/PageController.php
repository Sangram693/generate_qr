<?php

namespace App\Http\Controllers;

use TCPDF;
use App\Models\Beam;
use App\Models\Page;
use App\Models\Pole;
use App\Models\HighMast;
use Illuminate\Http\Request;
use App\Http\Requests\StorePageRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PageController extends Controller
{
    public function index()
{
    return Page::query()
        ->with('user') 
        ->orderBy('created_at', 'desc') 
        ->get()
        ->makeHidden('user_id'); 
}

    public function store(StorePageRequest $request)
    {
        $user = auth()->user();
        $request->merge(['user_id' => $user->id]);

        $request->validated([
            'product_type' => 'required|in:w-beam, pole, high-mast'
        ]);

        
        $rowNumber = $request->row_number;

        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Random Data');

        
        $sheet->setCellValue('A1', 'id');

        $productModel = match ($request->product_type) {
            'w-beam' => Beam::class,
            'pole' => Pole::class,
            'high-mast' => HighMast::class,
        };

        $baseUrl = match ($request->product_type) {
            'w-beam' => 'http://verify.utkarshsmart.in/api/w-beam/',
            'pole' => 'http://verify.utkarshsmart.in/api/pole/',
            'high-mast' => 'http://verify.utkarshsmart.in/api/high-mast/',
        };

        [$r, $g, $b] = match ($request->product_type) {
            'w-beam' => [56, 182, 255],
            'pole' => [255, 99, 71],
            'high-mast' => [34, 139, 34],
        };
        
        $data = [];
        for ($i = 2; $i <= $rowNumber + 1; $i++) {
            $randomValue = $this->generateRandomString(16);
            $sheet->setCellValue('A' . $i, $randomValue);
            $product = new $productModel();
            $product->id = $randomValue;
            $product->save();
            $data[] = $product->id;
        }

        
        $fileName = 'random_values_' . time() . '.xlsx';
        $filePath = 'excel_files/' . $fileName;
        $storagePath = storage_path('app/public/' . $filePath);
        $excelFile = $filePath;
        $filePath = 'storage/' . $filePath;

        
        if (!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0777, true);
        }

        
        $writer = new Xlsx($spreadsheet);
        $writer->save($storagePath);

        
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

        

        
        $page_width = $request->page_width;
        $page_height = $request->page_height;
        $qr_width = $request->qr_width;
        $qr_height = $request->qr_height;
        $margin_top = $request->margin_top;
        $margin_bottom = $request->margin_bottom;
        $margin_left = $request->margin_left;
        $margin_right = $request->margin_right;

        
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

        $pdf->SetFillColor(255, 255, 255, 0); 
$pdf->Rect(0, 0, $page_width, $page_height, 'F');
        
        foreach ($data as $productId) {
            $product = match ($request->product_type) {
                'w-beam' => Beam::find($productId),
                'pole' => Pole::find($productId),
                'high-mast' => HighMast::find($productId)
            };
            if (!$product) continue;
        
            
            $qrCodeSvg = QrCode::format('svg')->size($qr_width)->generate($baseUrl.$product->id);
            $tempFile = "{$tempPath}temp_qr_$count.svg";
            file_put_contents($tempFile, $qrCodeSvg);
        
            if (!file_exists($tempFile)) {
                return response()->json(['error' => "QR Code $count file not created"], 500);
            }

            $total_border_height = $qr_height + 13; 
            $pdf->SetAlpha(1);
            $pdf->SetFillColor(255, 255, 255); 
    $pdf->Rect($x - 1, $y - 1, $qr_width + 4, $total_border_height + 0.7, 'F');
            
$pdf->SetDrawColor($r, $g, $b); 
$pdf->SetLineWidth(1); 
$logo_height = 8; 
$padding = 1; 

$pdf->Rect($x - 1, $y - 1, $qr_width + 4, $total_border_height + 0.7, 'D'); 


$qr_x = $x + 1; 
$qr_y = $y; 
$pdf->ImageSVG($tempFile, $qr_x, $qr_y, $qr_width, $qr_height-1);

$line_y = $qr_y + $qr_height + 2; 
$pdf->SetDrawColor($r, $g, $b); 
$pdf->SetLineWidth(0.5); 
$pdf->Line($x -1, $line_y, $x + $qr_width + 3, $line_y);

$svgContent = file_get_contents($logoPath);
$logo_width = $qr_width;
$logo_x = $x + 1 +($qr_width - $logo_width) / 2; 
$logo_y = $qr_y + $qr_height + $padding +2; 
$pdf->Image(public_path('ut logo up.png'), $logo_x, $logo_y, $logo_width, $logo_height);

$pdf->SetFont('helvetica', 'B', 5); // Set Font (Bold)
    $pdf->SetTextColor(0, 0, 0); // Set Black Color
    $text_x = $logo_x + ($logo_width / 5); // Adjust text position to center
    $text_y = $logo_y-$padding*4; // Adjust text slightly above the center
    $pdf->Text($text_x, $text_y, $product->id); // Print Product ID


        
            
            $x += $qr_total_width + $margin_left;  
            $count++;
        
            if ($count % $columns == 0) {
                $x = $margin_left;
                $y += $qr_total_height + $margin_top;
            }
        
            if ($count % $qr_per_page == 0) {
                $pdf->AddPage();
                $pdf->SetFillColor(255, 255, 255, 0); 
$pdf->Rect(0, 0, $page_width, $page_height, 'F');
                $x = $margin_left;
                $y = $margin_top;
            }
        
            unlink($tempFile);
        }
        
        $pdfPath = storage_path('app/public/beam_qrcodes_' . '.pdf');
        $pdf->Output($pdfPath, 'F');
        
        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'PDF file not generated'], 500);
        }
        // unlink($excelFile);
        return response()->download($pdfPath);
        
        
    }

    private function generateRandomString($length = 16)
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, $length);
    }
}
