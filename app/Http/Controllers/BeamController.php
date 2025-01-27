<?php

namespace App\Http\Controllers;

use App\Models\Beam;
use App\Http\Requests\StoreBeamRequest;
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
    public function show($id)
    {

        // return $id;
        $beam = Beam::find($id);

        if (!$beam) {
            return response()->json(['message' => 'Beam not found'], 404);
        }

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
