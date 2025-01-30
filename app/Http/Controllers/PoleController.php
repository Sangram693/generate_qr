<?php

namespace App\Http\Controllers;

use App\Models\Pole;
use Illuminate\Http\Request;

class PoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
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
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $pole = Pole::find($id);
        if (!$pole) {
            return response()->json(['message' => 'Pole not found'], 404);
        }

        if($pole->status == 0){
            return response()->json(['message' => 'Pole not active'], 404);
         }

        $this->trackView('pole', $id, $request);

        return view('show_pole', ['pole' => $pole]);
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

     public function bulkUpdate(Request $request)
     {
         
         $validatedData = $request->validate([
             'ids' => 'required|array',
             'ids.*' => 'exists:poles,id', 
             'user_id' => 'nullable|exists:users,id'
         ]);
     
         
         $ids = $request->ids;
     
         
         $updateData = array_filter($request->except(['ids'])); 
     
         if (empty($updateData)) {
             return response()->json(['message' => 'No valid fields provided for update'], 400);
         }
     
         
         $updatedRows = Pole::whereIn('id', $ids)->update($updateData);
     
         return response()->json([
             'message' => $updatedRows > 0 ? 'Poles updated successfully' : 'No records updated',
             'updated_count' => $updatedRows
         ], 200);
     }
}
