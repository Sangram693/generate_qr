<?php

namespace App\Http\Controllers;

use Location;
use App\Models\Beam;
use App\Models\Stat;
use App\Models\Viewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $limit = $request->query('limit', 10);
        $limit = min($limit, 100);
        
        if (is_null($authUser->origin)) {
            // Superadmin: Paginate beams first, then group the current page's items by origin.
            $paginatedBeams = Beam::orderBy('created_at', 'desc')
                                    ->whereNotNull('batch_no')
                                    ->paginate($limit);
            $groupedBeams = $paginatedBeams->getCollection()->groupBy('origin');
            $paginatedBeams->setCollection($groupedBeams);
            
            // Get overall totals per origin (ignoring pagination)
            $groupTotals = Beam::selectRaw('origin, count(*) as total')
            ->where('origin', '<>', '')
            ->groupBy('origin')
            ->get()
            ->pluck('total', 'origin');
            
            // Build a custom response array with desired keys
            $data = [
                'current_page' => $paginatedBeams->currentPage(),
                'per_page'     => $paginatedBeams->perPage(),
                'total'        => $paginatedBeams->total(),
                'group_totals' => $groupTotals,
                'data'         => $groupedBeams,
            ];
            
            return response()->json(['beam' => $data]);
        } else {
            // Admin/User: Filter beams by the user's origin and paginate (no grouping needed).
            $paginatedBeams = Beam::orderBy('created_at', 'desc')
                                  ->where('origin', $authUser->origin)
                                  ->whereNotNull('batch_no')
                                  ->paginate($limit);
            
            $data = $paginatedBeams->toArray();
            // Optionally remove unwanted pagination keys if needed:
            unset(
                $data['first_page_url'],
                $data['last_page_url'],
                $data['next_page_url'],
                $data['prev_page_url'],
                $data['links'],
                $data['from'],
                $data['last_page'],
                $data['path'],
                $data['to']
            );
            $data = [
                'current_page' => $paginatedBeams->currentPage(),
                'per_page'     => $paginatedBeams->perPage(),
                'total'        => $paginatedBeams->total(),
                'data'         => [$authUser->origin => $data['data']],
            ];
            
            return response()->json(['beam' => $data]);
        }
    }
    
    


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBeamRequest $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    

     public function show($id, Request $request)
     {
         $beam = Beam::find($id);

         if (!$beam) {
             return response()->json(['message' => 'Beam not found'], 404);
         }

         if($beam->status == 0){
            return response()->json(['message' => 'Beam not active'], 404);
         }
 
        //  $this->trackView('beam', $id, $request);
 
         return view('show_beam', ['beam' => $beam]);
     }
 
     private function trackView($productType, $productId, Request $request)
     {
         $ipAddress = $request->ip();
         $userAgent = $request->header('User-Agent');
         $advertisingId = $request->header('X-Advertising-ID');
 
         $location = Location::get($ipAddress);
         $city = $location ? $location->cityName : 'Unknown';
         $country = $location ? $location->countryName : 'Unknown';
 
         
         $uniqueViewer = Viewer::where([
                 ['product_type', '=', $productType],
                 ['product_id', '=', $productId],
             ])
             ->where(function ($query) use ($advertisingId, $ipAddress) {
                 $query->where('advertising_id', $advertisingId)
                       ->orWhere('ip_address', $ipAddress);
             })
             ->first();
 
         if (!$uniqueViewer) {
             Viewer::create([
                 'product_type' => $productType,
                 'product_id' => $productId,
                 'advertising_id' => $advertisingId,
                 'ip_address' => $ipAddress,
                 'user_agent' => $userAgent,
                 'city' => $city,
                 'country' => $country,
                 'first_seen' => now(),
                 'last_seen' => now(),
             ]);
 
             Stat::firstOrCreate(
                 ['product_type' => $productType, 'product_id' => $productId],
                 ['total_hits' => 0, 'unique_hits' => 0]
             )->increment('unique_hits');
         } else {
             $uniqueViewer->update(['last_seen' => now()]);
         }
 
         Stat::firstOrCreate(
             ['product_type' => $productType, 'product_id' => $productId],
             ['total_hits' => 0, 'unique_hits' => 0]
         )->increment('total_hits');
     }
     
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Bulk update multiple Beam records from an Excel file.
 */

 public function bulkUpload(Request $request)
 {
     
     if (!$request->hasFile('excel_file')) {
         return response()->json(['message' => 'No file uploaded'], 400);
     }
 
     
     $file = $request->file('excel_file');
     $path = $file->getRealPath();
 
     
     $spreadsheet = IOFactory::load($path);
     $sheet = $spreadsheet->getActiveSheet();
     $data = $sheet->toArray(null, true, true, true); 
 
     
     $inserted = 0;
     $failed = [];
 
     
     foreach ($data as $index => $row) {
         if ($index === 1) continue; 
 
         try {
             Beam::create([
                    'id' => $row['A'],
                    'grade' => $row['B'] ?? null,
                    'batch_no' => $row['C'] ?? null,
                    'serial_no' => $row['D'] ?? null,
                    'origin' => $row['E'] ?? null,
                    'asp' => $row['F'] ?? null
             ]);
             $inserted++;
         } catch (\Exception $e) {
             $failed[] = "Row {$index}: Failed to insert record - " . $e->getMessage();
         }
     }
 
     
     return response()->json([
         'message' => 'Bulk upload completed',
         'inserted' => $inserted,
         'failed' => $failed,
     ], 200);
 }

public function bulkUpdate(Request $request)
{
    try{
        $validatedData = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:beams,id', 
            'grade' => 'nullable',
            'batch_no' => 'nullable',
            'origin' => 'nullable',
            'serial_no' => 'nullable',
            'user_id' => 'nullable|exists:users,id'
        ]);
    
        $ids = $request->ids;
    
        
        $updateData = array_filter($request->except(['ids'])); 
    
        if (empty($updateData)) {
            return response()->json(['message' => 'No valid fields provided for update'], 400);
        }
    
        $updatedRows = Beam::whereIn('id', $ids)->update($updateData);
    
        return response()->json([
            'message' => $updatedRows > 0 ? 'Beams updated successfully' : 'No records updated',
            'updated_count' => $updatedRows
        ], 200);
    }catch (\Exception $e) {
        \Log::error('Bulk Update Error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
    }
    
}

public function mappingStatus(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    if (is_null($authUser->origin)) {
        // Superadmin: Group beams by origin.
        $status = Beam::selectRaw("
                        origin, 
                        SUM(CASE WHEN batch_no IS NOT NULL THEN 1 ELSE 0 END) AS mapped_count, 
                        SUM(CASE WHEN batch_no IS NULL THEN 1 ELSE 0 END) AS unmapped_count
                    ")
                    ->groupBy('origin')
                    ->get();
        return response()->json(['mapping_status' => $status]);
    } else {
        // Admin/User: Filter beams by the user's origin.
        $status = Beam::selectRaw("
                        SUM(CASE WHEN batch_no IS NOT NULL THEN 1 ELSE 0 END) AS mapped_count, 
                        SUM(CASE WHEN batch_no IS NULL THEN 1 ELSE 0 END) AS unmapped_count
                    ")
                    ->where('origin', $authUser->origin)
                    ->first();

        $status['origin'] = $authUser->origin;
        // Wrap the counts in an array keyed by the user's origin.
        return response()->json(['mapping_status' => [$status]]);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        
    }
}
