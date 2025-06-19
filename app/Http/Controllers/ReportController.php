<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of reports with optional filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Report::query();

        // Apply date range filter if provided
        if ($request->filled(['start_date', 'end_date'])) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Apply product filter if provided
        if ($request->filled('product_name')) {
            $query->product($request->product_name);
        }

        // Apply delivery status filter if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'delivered') {
                $query->delivered();
            }
        }

        $reports = $query->latest()->get();

        // Get statistics for the view
        $stats = [
            'total_reports' => Report::count(),
            'pending_deliveries' => Report::pending()->count(),
            'delivered' => Report::delivered()->count()
        ];

        return view('reports.index', compact('reports', 'stats'));
    }

    /**
     * Update delivery date for multiple reports
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'required|exists:reports,id',
            'delivery_date' => 'required|date'
        ]);

        // Get the reports that will be updated
        $reports = Report::whereIn('id', $request->report_ids)
            ->whereNull('delivery_date')
            ->get();

        // Get unique page IDs from these reports
        $pageIds = $reports->pluck('page_id')->unique()->filter();

        // Update the reports
        Report::whereIn('id', $request->report_ids)
            ->whereNull('delivery_date')
            ->update(['delivery_date' => $request->delivery_date]);

        // Update page status for affected pages
        if ($pageIds->isNotEmpty()) {
            // For each affected page
            foreach ($pageIds as $pageId) {
                // Check if all reports for this page have delivery dates
                $page = \App\Models\Page::find($pageId);
                if ($page) {
                    $allReportsDelivered = !$page->reports()->whereNull('delivery_date')->exists();
                    $page->update([
                        'status' => $allReportsDelivered ? 1 : 0  // 1 for active (delivered), 0 for inactive (pending)
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Selected reports and related pages have been updated.');
    }
}