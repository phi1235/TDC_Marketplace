<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Tổng quan analytics (view/search cơ bản) theo khoảng thời gian
     */
    public function getOverview(array $filters = []): array
    {
        $from = $filters['from'] ?? now()->subDays(7)->startOfDay();
        $to = $filters['to'] ?? now()->endOfDay();
        $group = in_array(($filters['group'] ?? 'day'), ['day','week','month']) ? $filters['group'] : 'day';

        // group expression
        $groupExpr = match ($group) {
            'week' => "DATE_FORMAT(created_at, '%x-%v')", // ISO year-week
            'month' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "DATE(created_at)",
        };

        // Tổng số sự kiện theo loại
        $totals = DB::table('user_activities')
            ->select('event_name', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('event_name')
            ->pluck('total', 'event_name')
            ->toArray();

        // Time-series events/day
        $series = DB::table('user_activities')
            ->select(DB::raw("{$groupExpr} as g"), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw($groupExpr))
            ->orderBy('g')
            ->get();

        // Series by event
        $byEventRows = DB::table('user_activities')
            ->select(DB::raw("{$groupExpr} as g"), 'event_name', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw($groupExpr), 'event_name')
            ->orderBy('g')
            ->get();
        $seriesByEvent = [];
        foreach ($byEventRows as $r) {
            $seriesByEvent[$r->event_name][] = ['g' => $r->g, 'total' => (int) $r->total];
        }

        // Search metrics: total search, no-result rate
        $searchAgg = DB::table('user_activities')
            ->select(DB::raw("SUM(CASE WHEN JSON_EXTRACT(metadata, '$.result_count') IS NULL THEN 1 ELSE 1 END) as searches"),
                     DB::raw("SUM(CASE WHEN CAST(JSON_EXTRACT(metadata, '$.result_count') AS SIGNED) = 0 THEN 1 ELSE 0 END) as no_results"))
            ->where('event_name', 'search_performed')
            ->whereBetween('created_at', [$from, $to])
            ->first();

        // Top keywords
        $topKeywords = DB::table('user_activities')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.q')) as keyword"), DB::raw('COUNT(*) as total'))
            ->where('event_name', 'search_performed')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull(DB::raw("JSON_EXTRACT(metadata, '$.q')"))
            ->groupBy('keyword')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top listings viewed
        $topListings = DB::table('user_activities')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.listing_id')) as listing_id"), DB::raw('COUNT(*) as total'))
            ->where('event_name', 'listing_view')
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull(DB::raw("JSON_EXTRACT(metadata, '$.listing_id')"))
            ->groupBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.listing_id'))"))
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        if ($topListings->isNotEmpty()) {
            $ids = $topListings->pluck('listing_id')->filter()->map(fn($v) => (int) $v)->all();
            if (!empty($ids)) {
                $idToTitle = DB::table('listings')->whereIn('id', $ids)->pluck('title', 'id');
                $topListings->transform(function ($r) use ($idToTitle) {
                    $lid = (int) $r->listing_id;
                    $r->title = $idToTitle[$lid] ?? null;
                    return $r;
                });
            }
        }

        if ($topListings->isEmpty()) {
            // Fallback theo views_count để luôn có dữ liệu hiển thị
            $topListings = DB::table('listings')
                ->select(DB::raw('id as listing_id'), DB::raw('views_count as total'), 'title')
                ->where('views_count', '>', 0)
                ->orderByDesc('views_count')
                ->limit(10)
                ->get();
        }

        $searches = (int) ($searchAgg->searches ?? 0);
        $noResults = (int) ($searchAgg->no_results ?? 0);

        return [
            'totals' => $totals,
            'series' => $series,
            'series_by_event' => $seriesByEvent,
            'search' => [
                'total' => $searches,
                'no_results' => $noResults,
                'no_result_rate' => $searches > 0 ? round($noResults / $searches, 4) : 0,
                'top_keywords' => $topKeywords,
            ],
            'top_listings' => $topListings,
            'range' => [ 'from' => (string) $from, 'to' => (string) $to ],
            'group' => $group,
        ];
    }
}


