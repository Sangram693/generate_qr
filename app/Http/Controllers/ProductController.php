<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Beam;
use App\Models\Page;
use App\Models\Pole;
use App\Models\Product;
use App\Models\HighMast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
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
        $products = Product::get();
        
        return response()->json(['products' => $products]);

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
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    public function filter(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $limit = $request->query('limit', 1000);
    $limit = min($limit, 1000);
    $currentPage = $request->query('page', 1);

    // Retrieve filter parameters.
    $productName   = $request->input('product_name', 'all'); // expected: all, beam, HM, pole
    $batchNo       = $request->input('batch_no');
    $id            = $request->input('serial_no');
    $startDate     = $request->input('start_date');
    $endDate       = $request->input('end_date');
    $mappingStatus = $request->input('mapping_status'); // mapped or unmapped
    $originParam   = ($request->input('origin', 'all'));

    // Build a common filtering closure.
    $applyFilters = function ($query) use ($batchNo, $id, $startDate, $endDate, $mappingStatus, $authUser, $originParam) {
        if ($batchNo) {
            $query->where('batch_no', $batchNo);
        }
        if ($id) {
            $query->where('id', $id);
        }
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate));
            $end   = date('Y-m-d', strtotime($endDate));
            $query->whereBetween('updated_at', [$start, $end]);
        }
        if ($mappingStatus) {
            if (($mappingStatus) === 'mapped') {
                $query->whereNotNull('batch_no');
            } elseif (($mappingStatus) === 'unmapped') {
                $query->whereNull('batch_no');
            }
        }
        // For admin/users, force filtering by the user's origin.
        if (!is_null($authUser->origin)) {
            $query->where('origin', $authUser->origin);
        } else {
            // For superadmins, if an origin is provided and isn't "all", apply it.
            if ($originParam !== 'all') {
                $query->where('origin', $originParam);
            }
        }
        return $query;
    };

    $results = collect();
    if ($productName === 'all') {
        // Query all three tables.
        $beamResults     = $applyFilters(Beam::query())->get();
        $highMastResults = $applyFilters(HighMast::query())->get();
        $poleResults     = $applyFilters(Pole::query())->get();
        $results = collect($beamResults)->merge($highMastResults)->merge($poleResults);
    } elseif ($productName === 'MBCB') {
        $results = $applyFilters(Beam::query())->get();
    } elseif ($productName === 'HM') {
        $results = $applyFilters(HighMast::query())->get();
    } elseif ($productName === 'POLE') {
        $results = $applyFilters(Pole::query())->get();
    }

    // Sort results by updated_at descending.
    $results = $results->sortByDesc('updated_at')->values();

    // Transform each record to the desired structure.
    $transformed = $results->map(function ($item) {
        $productName = '';
        if ($item instanceof \App\Models\Beam) {
            $productName = 'MBCB';
        } elseif ($item instanceof \App\Models\HighMast) {
            $productName = 'HM';
        } elseif ($item instanceof \App\Models\Pole) {
            $productName = 'POLE';
        }
        $mappingStatus = $item->batch_no ? 'mapped' : 'unmapped';
        return [
            'product_name'   => $productName,
            'batch_no'       => $item->batch_no,
            'serial_no'      => $item->id,
            'mapping_status' => $mappingStatus,
            'origin'         => $item->origin,
            'date'           => $item->updated_at,
        ];
    });

    // Manually paginate the transformed collection.
    $paginatedResults = new \Illuminate\Pagination\LengthAwarePaginator(
        $transformed->forPage($currentPage, $limit),
        $transformed->count(),
        $limit,
        $currentPage,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return response()->json(['products' => $paginatedResults]);
}

public function report(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Retrieve required filter parameters.
    $productName = strtoupper($request->input('product_name', 'ALL')); // expected: ALL, MBCB, HM, or POLE
    $startDate   = $request->input('start_date');
    $endDate     = $request->input('end_date');
    
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Start date and end date are required'], 400);
    }
    
    // Convert dates to Y-m-d format.
    $start = date('Y-m-d 00:00:00', strtotime($startDate));
    $end   = date('Y-m-d 23:59:59', strtotime($endDate));
    
    // If the user is not a superadmin, filter by their origin.
    $originFilter = null;
    if (!is_null($authUser->origin)) {
        $originFilter = $authUser->origin;
    }
    
    // Prepare a helper function to run the report query on a given model.
    $runReport = function($modelQuery, $productName) use ($start, $end, $originFilter) {
        if ($originFilter) {
            $modelQuery->where('origin', $originFilter);
        }
        return $modelQuery->whereBetween('updated_at', [$start, $end])
            ->selectRaw("DATE(updated_at) as date, count(*) as quantity")
            ->groupBy('date')
            ->get()
            ->map(function ($item) use ($productName) {
                return [
                    'date' => $item->date,
                    'quantity' => $item->quantity,
                    'product_name' => $productName,
                ];
            });
    };
    
    $data = [];
    if ($productName === 'ALL') {
        // For "ALL", get report for each product type and merge into a single array.
        $beamReport     = $runReport(Beam::query(), 'MBCB');
        $highMastReport = $runReport(HighMast::query(), 'HM');
        $poleReport     = $runReport(Pole::query(), 'POLE');
        
        // Merge all reports into a single array.
        $data = array_merge($beamReport->toArray(), $highMastReport->toArray(), $poleReport->toArray());
    } elseif ($productName === 'MBCB') {
        $data = $runReport(Beam::query(), 'MBCB');
    } elseif ($productName === 'HM') {
        $data = $runReport(HighMast::query(), 'HM');
    } elseif ($productName === 'POLE') {
        $data = $runReport(Pole::query(), 'POLE');
    } else {
        return response()->json(['error' => 'Invalid product_name value'], 400);
    }
    
    return response()->json(['report' => $data]);
}


public function total(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    if (is_null($authUser->origin)) {
        // Superadmin: Merge data from all three tables (without user-based grouping),
        // then group by origin.
        $beamResults = Beam::all();
        $highMastResults = HighMast::all();
        $poleResults = Pole::all();
        $merged = collect($beamResults)
                    ->merge($highMastResults)
                    ->merge($poleResults);
        // Optionally filter out records without a valid origin.
        $merged = $merged->filter(function($item) {
            return !empty($item->origin) && !empty($item->batch_no);
        });
        $totalCount = $merged->count();
        $grouped = $merged->groupBy('origin');
        $data = [];
        foreach ($grouped as $origin => $records) {
            $quantity = $records->count();
            $percentage = $totalCount > 0 ? round(($quantity / $totalCount) * 100, 2) : 0;
            $data[$origin] = [
                'quantity'   => $quantity,
                'percentage' => $percentage,
            ];
        }
        return response()->json(['total' => $data]);
    } else {
        // Admin: Only consider records matching the admin's origin.
        $beamResults = Beam::where('origin', $authUser->origin)->get();
        $highMastResults = HighMast::where('origin', $authUser->origin)->get();
        $poleResults = Pole::where('origin', $authUser->origin)->get();
        $merged = collect($beamResults)
                    ->merge($highMastResults)
                    ->merge($poleResults);
                    
        // Filter records that have a user_id so we can group by user.
        $merged = $merged->filter(function($item) {
            return !is_null($item->user_id) && !empty($item->batch_no);
        });
        $totalCount = $merged->count();
        $grouped = $merged->groupBy('user_id');
        
        // Build an associative array keyed by the username.
        $data = [];
        foreach ($grouped as $userId => $records) {
            $quantity = $records->count();
            $percentage = $totalCount > 0 ? round(($quantity / $totalCount) * 100, 2) : 0;
            // Fetch the user's username from the User model.
            $user = \App\Models\User::find($userId);
            $username = $user ? $user->user_name : "Unknown";
            $data[$username] = [
                'quantity'   => $quantity,
                'percentage' => $percentage,
                'origin'     => $records->first()->origin,
            ];
        }
        return response()->json(['total' => $data]);
    }
}


public function graph(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Get time_frame parameter; default to 'fy'
    $timeFrame = ($request->input('time_frame', 'fy'));
    if (!in_array($timeFrame, ['fy', 'monthly'])) {
        return response()->json(['error' => 'Invalid time_frame value'], 400);
    }
    
    // Retrieve data from the three tables.
    if (is_null($authUser->origin)) {
        // Superadmin: no origin filtering.
        $beamResults = Beam::all();
        $highMastResults = HighMast::all();
        $poleResults = Pole::all();
    } else {
        // Admin: filter by the admin's origin.
        $beamResults = Beam::where('origin', $authUser->origin)->get();
        $highMastResults = HighMast::where('origin', $authUser->origin)->get();
        $poleResults = Pole::where('origin', $authUser->origin)->get();
    }
    
    // Merge the collections.
    $merged = collect($beamResults)->merge($highMastResults)->merge($poleResults);
    
    // Only include records that are "mapped" (i.e. batch_no is not empty).
    $merged = $merged->filter(function($item) {
        return !empty($item->batch_no);
    });
    
    // Filter records by date based on time_frame.
    if ($timeFrame === 'fy') {
        // Determine the current Indian financial year boundaries.
        $today = Carbon::now();
        if ($today->month >= 4) {
            $fyStart = Carbon::create($today->year, 4, 1, 0, 0, 0);
            $fyEnd = Carbon::create($today->year + 1, 3, 31, 23, 59, 59);
        } else {
            $fyStart = Carbon::create($today->year - 1, 4, 1, 0, 0, 0);
            $fyEnd = Carbon::create($today->year, 3, 31, 23, 59, 59);
        }
        $merged = $merged->filter(function($item) use ($fyStart, $fyEnd) {
            $date = Carbon::parse($item->updated_at);
            return $date->between($fyStart, $fyEnd);
        });
    } else { // monthly
        // Filter to include only records from the current month.
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $merged = $merged->filter(function($item) use ($currentMonth, $currentYear) {
            $date = Carbon::parse($item->updated_at);
            return $date->year == $currentYear && $date->month == $currentMonth;
        });
    }
    
    // Define a helper to group records by time period.
    $groupByTime = function($item) use ($timeFrame) {
        $date = Carbon::parse($item->updated_at);
        if ($timeFrame === 'fy') {
            // Group by month within the financial year.
            // We'll simply use the month abbreviation.
            return $date->format('M'); // e.g., "Apr", "May", etc.
        } else {
            // For "monthly", group by week-of-month.
            // Calculate week-of-month as ceil(day/7), capped at 4.
            $day = $date->day;
            $weekOfMonth = ceil($day / 7);
            if ($weekOfMonth > 4) {
                $weekOfMonth = 4;
            }
            return 'W' . $weekOfMonth;
        }
    };
    
    if (is_null($authUser->origin)) {
        // For superadmins: Group by origin first, then by time period.
        $grouped = $merged->groupBy('origin')->map(function($records) use ($groupByTime) {
            return $records->groupBy($groupByTime)->map(function($items) {
                return $items->count();
            });
        });
        return response()->json(['graph' => $grouped]);
    } else {
        // For admins: Group by time period only.
        $grouped = $merged->groupBy($groupByTime)->map(function($items) {
            return $items->count();
        });
        return response()->json(['graph' => $grouped]);
    }
}


public function quarter(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Determine origin filter.
    // For superadmin, we can allow an optional 'origin' parameter.
    if (is_null($authUser->origin)) {
        $originParam = ($request->input('origin', 'null'));
        $originFilter = ($originParam !== 'null') ? $originParam : null;
    } else {
        $originFilter = $authUser->origin;
    }

    // Determine the boundaries for the current Indian financial year.
    // Indian financial year: April 1 to March 31.
    $today = Carbon::now();
    if ($today->month >= 4) {
        $fyStart = Carbon::create($today->year, 4, 1, 0, 0, 0);
        $fyEnd   = Carbon::create($today->year + 1, 3, 31, 23, 59, 59);
    } else {
        $fyStart = Carbon::create($today->year - 1, 4, 1, 0, 0, 0);
        $fyEnd   = Carbon::create($today->year, 3, 31, 23, 59, 59);
    }

    // Define a helper to determine the quarter based on the updated_at date.
    $getQuarter = function($date) {
        $month = Carbon::parse($date)->month;
        if ($month >= 4 && $month <= 6) {
            return 'Q1';
        } elseif ($month >= 7 && $month <= 9) {
            return 'Q2';
        } elseif ($month >= 10 && $month <= 12) {
            return 'Q3';
        } else {
            return 'Q4';
        }
    };

    // Retrieve and count records for each model.
    // For each, we:
    // - Only include records where batch_no is not empty.
    // - Filter by updated_at between fyStart and fyEnd.
    // - If an origin filter is set, add that condition.
    $beamCounts = Beam::whereNotNull('batch_no')
        ->whereBetween('updated_at', [$fyStart, $fyEnd])
        ->when($originFilter, function($query) use ($originFilter) {
            return $query->where('origin', $originFilter);
        })
        ->get()
        ->groupBy(function($item) use ($getQuarter) {
            return $getQuarter($item->updated_at);
        })
        ->map->count();

    $highMastCounts = HighMast::whereNotNull('batch_no')
        ->whereBetween('updated_at', [$fyStart, $fyEnd])
        ->when($originFilter, function($query) use ($originFilter) {
            return $query->where('origin', $originFilter);
        })
        ->get()
        ->groupBy(function($item) use ($getQuarter) {
            return $getQuarter($item->updated_at);
        })
        ->map->count();

    $poleCounts = Pole::whereNotNull('batch_no')
        ->whereBetween('updated_at', [$fyStart, $fyEnd])
        ->when($originFilter, function($query) use ($originFilter) {
            return $query->where('origin', $originFilter);
        })
        ->get()
        ->groupBy(function($item) use ($getQuarter) {
            return $getQuarter($item->updated_at);
        })
        ->map->count();

    // Prepare default quarters.
    $quarters = ['Q1' => 0, 'Q2' => 0, 'Q3' => 0, 'Q4' => 0];
    $result = [];
    foreach ($quarters as $q => $default) {
        $beam = $beamCounts->get($q, 0);
        $hm   = $highMastCounts->get($q, 0);
        $pole = $poleCounts->get($q, 0);
        $total = $beam + $hm + $pole;
        $result[$q] = [
            'MBCB' => $beam,
            'HM' => $hm,
            'POLE' => $pole,
            'total' => $total,
        ];
    }

    return response()->json(['quarter' => $result]);
}

public function mapped(Request $request)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // For superadmin, allow an optional 'origin' parameter; for admin, force using their origin.
    if (is_null($authUser->origin)) {
        $originParam = ($request->input('origin', 'all'));
        // If 'all' is provided, then we won't filter by origin; otherwise, filter by the provided value.
        $filterOrigin = ($originParam !== 'all') ? $originParam : null;

        // Retrieve all records from the three tables.
        $beamResults = Beam::all();
        $highMastResults = HighMast::all();
        $poleResults = Pole::all();
    } else {
        
        $filterOrigin = $authUser->origin;
        // Admin: only fetch records with the admin's origin.
        $beamResults = Beam::where('origin', $filterOrigin)->get();
        $highMastResults = HighMast::where('origin', $filterOrigin)->get();
        $poleResults = Pole::where('origin', $filterOrigin)->get();
        
    }

    // Merge the collections.
    $merged = collect($beamResults)
                ->merge($highMastResults)
                ->merge($poleResults);

                

    // If a filterOrigin is set (for admin or if superadmin provided a specific origin), filter the collection.
    if ($filterOrigin) {
        $merged = $merged->filter(function ($item) use ($filterOrigin) {
            return ($item->origin) === ($filterOrigin);
        });
    }

    
    
    if (is_null($authUser->origin)) {
        // Superadmin: Calculate both mapped and unmapped counts.
        $mappedCount = $merged->filter(function ($item) {
            return !empty($item->batch_no);
        })->count();

        $unmappedCount = $merged->filter(function ($item) {
            return empty($item->batch_no);
        })->count();

        $total = $mappedCount + $unmappedCount;
        $mappedPercentage = $total > 0 ? round(($mappedCount / $total) * 100, 2) : 0;
        $unmappedPercentage = $total > 0 ? round(($unmappedCount / $total) * 100, 2) : 0;

        $result = [
            'mapped_quantity'    => $mappedCount,
            'unmapped_quantity'  => $unmappedCount,
            'total'              => $total,
            'mapped_percentage'  => $mappedPercentage,
            'unmapped_percentage'=> $unmappedPercentage,
        ];
        return response()->json(['mapped' => $result]);
    } else {
        // Admin: Only show mapped records.
        $mappedCount = $merged->filter(function ($item) {
            return !empty($item->batch_no);
        })->count();
        
        $total = $mappedCount;
        $mappedPercentage = $total > 0 ? 100 : 0;

        $result = [
            'mapped_quantity'   => $mappedCount,
            'total'             => $total,
            'mapped_percentage' => $mappedPercentage,
        ];
        return response()->json(['mapped' => $result]);
    }
}

public function bulkMapped(Request $request)
{
    ini_set('max_execution_time', 300); // Increase execution time

    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $batchNo = $request->input('batch_no');
    $grade = $request->input('grade');
    $asp = $request->input('asp');
    if (!$batchNo) {
        return response()->json(['message' => 'Batch number is required'], 400);
    }

    if (strlen($batchNo) > 50) {
    return response()->json(['message' => 'Batch number must be within 50 characters long'], 400);
}

    $headerId = $request->input('header_id');
    if (!$headerId) {
        return response()->json(['message' => 'Header ID is required'], 400);
    }

    $totalScan = $request->input('total_scan');
    if (!$totalScan || !is_numeric($totalScan)) {
        return response()->json(['message' => 'Total scan is required and must be numeric'], 400);
    }

    try {
        $page = Page::find($headerId);
        if (!$page) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        $product = $page->product;
        $startIndex = $page->start_index;
        $totalRows = $page->total_rows;
        $endIndex = min($startIndex + $totalScan - 1, $totalRows); // Do not exceed total rows

        if ($endIndex < $startIndex) {
            return response()->json(['message' => 'Invalid range for start_index and total_scan'], 400);
        }

        if($totalScan > ($totalRows - $startIndex +1)) {
            return response()->json(['message' => 'Total scan exceeds remaining QR'], 400);
        }

        $filePath = str_replace('storage/', '', $page->excel_file);
        $storagePath = public_path("storage/{$filePath}");
        
        if (!file_exists($storagePath)) {
            return response()->json(['message' => 'Excel file not found'], 400);
        }
        
        $spreadsheet = IOFactory::load($storagePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $origin = ($authUser->role === 'super_admin' && $request->has('origin')) 
                    ? $request->input('origin') 
                    : $authUser->origin;

        $updatedRecords = [];

        foreach ($data as $index => $row) {
            if ($index === 1) continue; // Skip header
            if ($index < $startIndex) continue; // Skip rows before start_index
            if ($index > $endIndex) break; // Stop after end_index

            $id = $row['A'] ?? null;
            if (!$id) continue;

            $updateData = [
                'grade'    => $grade,
                'batch_no' => $batchNo,
                'origin'   => $origin,
                'asp'      => $asp
            ];

            $updateData = array_filter($updateData, fn($value) => !is_null($value));
            if (empty($updateData)) continue;

            $modelClass = match ($product) {
                'w-beam'    => Beam::class,
                'high-mast' => HighMast::class,
                'pole'      => Pole::class,
                default     => null,
            };

            if (!$modelClass) {
                return response()->json(['message' => 'Invalid product type'], 400);
            }

            $affectedRows = $modelClass::where('id', $id)->update($updateData);
            if ($affectedRows) {
                $updatedRecords[] = [
                    'id'           => $id,
                    'product'      => $product === 'w-beam' ? 'MBCB' : ($product === 'high-mast' ? 'HM' : 'POLE'),
                    'mapped_date'  => now()->toDateTimeString()
                ];
            }
        }

        // Update page start_index and possibly isMapped
        if (!empty($updatedRecords)) {
            $page->start_index = $endIndex + 1;
            if ($page->start_index > $totalRows) {
                $page->isMapped = true;
            }
            $page->save();
        }

        return response()->json($updatedRecords, 200);
    } catch (\Exception $e) {
        \Log::error('Bulk Update Error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
    }
}

public function unmappedSingle(Request $request)
{
    ini_set('max_execution_time', 300); // Increase execution time

    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Validate and retrieve the data from the request
    $id = $request->input('id');
    if (!$id) {
        return response()->json(['message' => 'ID is required'], 400);
    }

    $product = $request->input('product');
    if (!$product) {
        return response()->json(['message' => 'Product is required'], 400);
    }

    try {
        // Determine the model class based on the product type
        $modelClass = match ($product) {
            'MBCB'    => Beam::class,
            'HM' => HighMast::class,
            'POLE'      => Pole::class,
            default     => null,
        };

        if (!$modelClass) {
            return response()->json(['message' => 'Invalid product type'], 400);
        }

        // Attempt to unmap the single record
        $affectedRows = $modelClass::where('id', $id)->update([
            'grade'    => null,
            'batch_no' => null,
            'origin'   => null,
            'asp'      => null,
            'user_id'  => null,
        ]);

        if ($affectedRows) {
            // Record unmapped successfully
            $unmappedRecord = [
                'id'          => $id,
                'product'     => $product,
                'unmapped_at' => now()->toDateTimeString()
            ];

            return response()->json($unmappedRecord, 200);
        } else {
            return response()->json(['message' => 'Record not found or already unmapped'], 404);
        }
    } catch (\Exception $e) {
        \Log::error('Single Unmap Error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
    }
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}