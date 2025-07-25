<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WaitingListController extends Controller
{
    public function index(Request $request)
    {
        // Optional filters
        $query = WaitingList::query();
        if ($request->filled('source')) {
            $query->where('signup_source', $request->source);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $list = $query->orderBy('created_at', 'desc')
                      ->paginate(15);

        return response()->json($list);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:waiting_lists,email',
            'signup_source' => 'nullable|string|max:100',
        ]);

        $entry = WaitingList::create($data);

        return response()->json($entry, 201);
    }

    public function show($id)
    {
        $entry = WaitingList::findOrFail($id);
        return response()->json($entry);
    }

    public function update(Request $request, $id)
    {
        $entry = WaitingList::findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'email'         => [
                                  'sometimes',
                                  'required',
                                  'email',
                                  Rule::unique('waiting_lists')->ignore($entry->id),
                               ],
            'signup_source' => 'sometimes|nullable|string|max:100',
        ]);

        $entry->update($data);

        return response()->json($entry);
    }

    public function destroy($id)
    {
        WaitingList::destroy($id);
        return response()->json(null, 204);
    }

        /**
     * GET /api/waiting-list/stats
     */
    public function stats()
    {
        // 1. Total signups
        $total = WaitingList::count();

        // 2. Count per source
        $bySource = WaitingList::query()
            ->selectRaw('signup_source, COUNT(*) as count')
            ->groupBy('signup_source')
            ->get()
            ->pluck('count', 'signup_source');

        // 3. Daily counts for last 30 days
        $start = now()->subDays(29)->startOfDay();
        $daily = WaitingList::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // 4. Peak signup day
        $peak = WaitingList::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderByDesc('count')
            ->limit(1)
            ->first(['date', 'count']);

        return response()->json([
            'total_signups'    => $total,
            'signups_by_source'=> $bySource,
            'daily_trend'      => $daily,
            'peak_day'         => $peak ? $peak->date : null,
            'peak_count'       => $peak ? $peak->count : 0,
        ]);
    }



    public function exportCsv()
    {
        // Prepare the stats data
        $total = WaitingList::count();
        $bySource = WaitingList::query()
            ->selectRaw('signup_source, COUNT(*) as count')
            ->groupBy('signup_source')
            ->pluck('count', 'signup_source')
            ->toArray();

        $start = now()->subDays(29)->startOfDay();
        $daily = WaitingList::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="waiting_list_stats.csv"',
        ];

        $callback = function() use ($total, $bySource, $daily) {
            $handle = fopen('php://output', 'w');

            // Write header row
            fputcsv($handle, ['Metric', 'Value']);

            // Total signups
            fputcsv($handle, ['Total Signups', $total]);

            // Blank line
            fputcsv($handle, []);

            // Signups by source
            fputcsv($handle, ['Signups by Source']);
            fputcsv($handle, ['Source', 'Count']);
            foreach ($bySource as $source => $count) {
                fputcsv($handle, [$source, $count]);
            }

            // Blank line
            fputcsv($handle, []);

            // Daily trend
            fputcsv($handle, ['Daily Trend (Last 30 Days)']);
            fputcsv($handle, ['Date', 'Count']);
            foreach ($daily as $date => $count) {
                fputcsv($handle, [$date, $count]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

}
