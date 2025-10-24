<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchAnalyticsController extends Controller
{
    /**
     * 📊 Lưu thông tin search analytics
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'query' => 'required|string|max:255',
                'timestamp' => 'required|date',
                'elasticsearch' => 'required|array',
                'solr' => 'required|array',
                'winner' => 'required|string|in:elasticsearch,solr,tie',
                'user_id' => 'nullable|integer'
            ]);

            // Lưu vào database
            DB::table('search_analytics')->insert([
                'query' => $data['query'],
                'timestamp' => now(), // Use Laravel's now() instead of the provided timestamp
                'elasticsearch_response_time' => $data['elasticsearch']['response_time'],
                'elasticsearch_result_count' => $data['elasticsearch']['result_count'],
                'elasticsearch_success' => $data['elasticsearch']['success'],
                'solr_response_time' => $data['solr']['response_time'],
                'solr_result_count' => $data['solr']['result_count'],
                'solr_success' => $data['solr']['success'],
                'winner' => $data['winner'],
                'user_id' => $data['user_id'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Failed to save search analytics: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save analytics'], 500);
        }
    }

    /**
     * 📈 Lấy thống kê search analytics
     */
    public function getStats(Request $request)
    {
        try {
            $dateFrom = $request->get('date_from', now()->subDays(7));
            $dateTo = $request->get('date_to', now());

            $stats = DB::table('search_analytics')
                ->whereBetween('timestamp', [$dateFrom, $dateTo])
                ->selectRaw('
                    COUNT(*) as total_searches,
                    AVG(elasticsearch_response_time) as avg_es_time,
                    AVG(solr_response_time) as avg_solr_time,
                    SUM(CASE WHEN winner = "elasticsearch" THEN 1 ELSE 0 END) as es_wins,
                    SUM(CASE WHEN winner = "solr" THEN 1 ELSE 0 END) as solr_wins,
                    AVG(elasticsearch_result_count) as avg_es_results,
                    AVG(solr_result_count) as avg_solr_results
                ')
                ->first();

            $popularQueries = DB::table('search_analytics')
                ->whereBetween('timestamp', [$dateFrom, $dateTo])
                ->selectRaw('query, COUNT(*) as search_count')
                ->groupBy('query')
                ->orderByDesc('search_count')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'popular_queries' => $popularQueries
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get search analytics: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to get analytics'], 500);
        }
    }

    /**
     * 🔍 Lấy lịch sử search của user
     */
    public function getUserHistory(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $history = DB::table('search_analytics')
                ->where('user_id', $userId)
                ->orderByDesc('timestamp')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get user search history: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to get history'], 500);
        }
    }

    /**
     * 📊 Lấy so sánh hiệu năng theo thời gian
     */
    public function getPerformanceComparison(Request $request)
    {
        try {
            $dateFrom = $request->get('date_from', now()->subDays(30));
            $dateTo = $request->get('date_to', now());

            $comparison = DB::table('search_analytics')
                ->whereBetween('timestamp', [$dateFrom, $dateTo])
                ->selectRaw('
                    DATE(timestamp) as date,
                    AVG(elasticsearch_response_time) as avg_es_time,
                    AVG(solr_response_time) as avg_solr_time,
                    COUNT(*) as search_count
                ')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $comparison
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to get performance comparison: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to get comparison'], 500);
        }
    }
}
