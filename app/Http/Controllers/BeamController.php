<?php

namespace App\Http\Controllers;

use App\Models\Beam;
use App\Http\Requests\StoreBeamRequest;
use Illuminate\Http\Request;
use App\Models\Stat;
use App\Models\Viewer;
use Location;
use App\Http\Requests\UpdateBeamRequest;

class BeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBeamRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    

     public function show($id, Request $request)
     {
         // Find the Beam record
         $beam = Beam::find($id);
        //  if (!$beam) {
        //      return response()->json(['message' => 'Beam not found'], 404);
        //  }
     
         // Get user details
         $ipAddress = $request->ip(); // Get the user's IP address
         $userAgent = $request->header('User-Agent');
         $advertisingId = $request->header('X-Advertising-ID'); // Assuming advertising ID is sent in request header
     
         // Get location using `stevebauman/location`
         $location = Location::get($ipAddress);
         $city = $location ? $location->cityName : 'Unknown';
         $country = $location ? $location->countryName : 'Unknown';
     
         // Check if user is unique (based on advertising_id or IP address)
         $uniqueViewer = Viewer::where(function ($query) use ($advertisingId, $ipAddress) {
                 $query->where('advertising_id', $advertisingId)
                       ->orWhere('ip_address', $ipAddress);
             })
             ->first();
     
         if (!$uniqueViewer) {
             // Insert new unique viewer using Eloquent ORM
             Viewer::create([
                 'advertising_id' => $advertisingId,
                 'ip_address' => $ipAddress,
                 'user_agent' => $userAgent,
                 'city' => $city,
                 'country' => $country,
                 'first_seen' => now(),
                 'last_seen' => now(),
             ]);
     
             // Increment unique_hits using Eloquent ORM
             Stat::firstOrCreate(['id' => 1])->increment('unique_hits');
         } else {
             // Update last_seen for returning viewer
             $uniqueViewer->update([
                 'last_seen' => now(),
             ]);
         }
     
         // Increment total_hits using Eloquent ORM
         Stat::firstOrCreate(['id' => 1])->increment('total_hits');
     
         return view('show_beam', ['beam' => $beam]);
     }
     
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Bulk update multiple Beam records from an Excel file.
 */

 public function bulkUpload(Request $request)
 {
     // Validate if a file is uploaded
     if (!$request->hasFile('excel_file')) {
         return response()->json(['message' => 'No file uploaded'], 400);
     }
 
     // Get uploaded file
     $file = $request->file('excel_file');
     $path = $file->getRealPath();
 
     // Load the Excel file
     $spreadsheet = IOFactory::load($path);
     $sheet = $spreadsheet->getActiveSheet();
     $data = $sheet->toArray(null, true, true, true); // Convert to array
 
     // Initialize counters
     $inserted = 0;
     $failed = [];
 
     // Loop through each row, starting from row 2 (assuming row 1 is headers)
     foreach ($data as $index => $row) {
         if ($index === 1) continue; // Skip headers
 
         try {
             Beam::create([
                    'id' => $row['A'],
                 'name' => $row['B'] ?? null,
                 'description' => $row['C'] ?? null,
                 'model_number' => $row['D'] ?? null,
                 'serial_number' => $row['E'] ?? null,
                 'batch_number' => $row['F'] ?? null,
                 'manufacturer' => $row['G'] ?? null,
                 'beam_type' => $row['H'] ?? null,
                 'beam_shape' => $row['I'] ?? null,
                 'beam_length' => $row['J'] ?? null,
                 'beam_width' => $row['K'] ?? null,
                 'beam_height' => $row['L'] ?? null,
                 'beam_weight' => $row['M'] ?? null,
             ]);
             $inserted++;
         } catch (\Exception $e) {
             $failed[] = "Row {$index}: Failed to insert record - " . $e->getMessage();
         }
     }
 
     // Return summary response
     return response()->json([
         'message' => 'Bulk upload completed',
         'inserted' => $inserted,
         'failed' => $failed,
     ], 200);
 }

public function bulkUpdate(UpdateBeamRequest $request)
{
    // Validate if a file is uploaded
    if (!$request->hasFile('excel_file')) {
        return response()->json(['message' => 'No file uploaded'], 400);
    }

    // Get uploaded file
    $file = $request->file('excel_file');
    $path = $file->getRealPath();

    // Load the Excel file
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true); // Convert to array

    // Initialize counters
    $updated = 0;
    $notFound = [];
    $failed = [];

    // Loop through each row, starting from row 2 (assuming row 1 is headers)
    foreach ($data as $index => $row) {
        if ($index === 1) continue; // Skip headers

        $beamId = $row['A'] ?? null; // Get Beam ID

        if (!$beamId) {
            $failed[] = "Row {$index}: Missing Beam ID";
            continue;
        }

        // Find the Beam record
        $beam = Beam::find($beamId);

        if (!$beam) {
            $notFound[] = "Row {$index}: Beam ID {$beamId} not found";
            continue;
        }

        // Update Beam fields
        try {
            $beam->name = $row['B'] ?? $beam->name;
            $beam->description = $row['C'] ?? $beam->description;
            $beam->model_number = $row['D'] ?? $beam->model_number;
            $beam->serial_number = $row['E'] ?? $beam->serial_number;
            $beam->bach_number = $row['F'] ?? $beam->bach_number;
            $beam->manufacturer = $row['G'] ?? $beam->manufacturer;
            $beam->beam_type = $row['H'] ?? $beam->beam_type;
            $beam->beam_shape = $row['I'] ?? $beam->beam_shape;
            $beam->beam_length = $row['J'] ?? $beam->beam_length;
            $beam->beam_width = $row['K'] ?? $beam->beam_width;
            $beam->beam_height = $row['L'] ?? $beam->beam_height;
            $beam->beam_weight = $row['M'] ?? $beam->beam_weight;

            $beam->save();
            $updated++;

        } catch (\Exception $e) {
            $failed[] = "Row {$index}: Failed to update Beam ID {$beamId} - " . $e->getMessage();
        }
    }

    // Return summary response
    return response()->json([
        'message' => 'Bulk update completed',
        'updated' => $updated,
        'not_found' => $notFound,
        'failed' => $failed,
    ], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
