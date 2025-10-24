<?php

namespace App\Http\Controllers;

use App\Services\SolrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolrSearchController extends Controller
{
    protected $solr;

    public function __construct(SolrService $solr)
    {
        $this->solr = $solr;
    }

    /**
     * 🔍 Tìm kiếm chính (nhấn Enter) - Solr version
     */
    public function index(Request $request)
    {
        $keyword = trim($request->get('q', ''));

        if (empty($keyword)) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'message' => 'No keyword provided',
                'engine' => 'solr'
            ]);
        }

        // ✅ Query Solr với Vietnamese text support
        $query = [
            'q' => $keyword,
            'defType' => 'edismax',
            'qf' => 'title^3 description^1',
            'pf' => 'title^3 description^1',
            'mm' => '80%',
            'rows' => 30,
            'start' => 0,
            'wt' => 'json',
            'hl' => 'true',
            'hl.fl' => 'title,description',
            'hl.simple.pre' => '<mark>',
            'hl.simple.post' => '</mark>',
        ];

        $startTime = microtime(true);
        $result = $this->solr->customSearch($query);
        $endTime = microtime(true);
        
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $docs = $result['response']['docs'] ?? [];
        $count = $result['response']['numFound'] ?? 0;

        /**
         * 🧠 Ghi lại lịch sử tìm kiếm theo user
         */
        try {
            $userId = auth()->id() ?? 0;
            $this->logSearchHistory($keyword, $userId, $count, $responseTime);
        } catch (\Throwable $e) {
            Log::error('❌ Save Solr search history failed: ' . $e->getMessage());
        }

        return response()->json([
            'count' => $count,
            'data' => $docs,
            'response_time' => round($responseTime, 2),
            'engine' => 'solr',
            'highlighting' => $result['highlighting'] ?? []
        ]);
    }

    /**
     * 📜 Trả về 10 từ khoá user đã tìm gần đây (theo timestamp desc) - Solr
     */
    public function history(Request $request)
    {
        $userId = auth()->id() ?? 0;

        $query = [
            'q' => 'user_id:' . $userId,
            'sort' => 'timestamp desc',
            'rows' => 10,
            'fl' => 'keyword,timestamp,results_count,response_time',
            'wt' => 'json'
        ];

        $result = $this->solr->customSearch($query);
        $docs = $result['response']['docs'] ?? [];

        $history = collect($docs)->map(function ($doc) {
            return [
                'keyword' => $doc['keyword'] ?? '',
                'timestamp' => $doc['timestamp'] ?? null,
                'results_count' => $doc['results_count'] ?? 0,
                'response_time' => $doc['response_time'] ?? 0,
            ];
        })->values();

        return response()->json([
            'history' => $history,
            'engine' => 'solr'
        ]);
    }

    /**
     * 💡 Gợi ý realtime (dropdown như Google) - Solr
     */
    public function suggestions(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json(['suggestions' => [], 'engine' => 'solr']);
        }

        $suggestions = $this->solr->suggest($keyword, 10);
        
        $suggestList = $suggestions['suggest']['mySuggester']['suggestions'] ?? [];
        
        $suggestions = collect($suggestList)
            ->pluck('term')
            ->filter()
            ->unique()
            ->values();

        return response()->json([
            'suggestions' => $suggestions,
            'engine' => 'solr'
        ]);
    }

    /**
     * 🧹 Xoá lịch sử tìm kiếm của user hiện tại - Solr
     */
    public function clearHistory()
    {
        try {
            $userId = auth()->id() ?? 0;
            
            $query = "user_id:" . $userId;
            $success = $this->solr->deleteByQuery($query);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'History cleared successfully!' : 'Failed to clear history',
                'engine' => 'solr'
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Solr clearHistory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing history: ' . $e->getMessage(),
                'engine' => 'solr'
            ], 500);
        }
    }

    /**
     * 🔍 Tìm kiếm với filters - Solr
     */
    public function searchWithFilters(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        $filters = $request->get('filters', []);

        if (empty($keyword)) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'message' => 'No keyword provided',
                'engine' => 'solr'
            ]);
        }

        $startTime = microtime(true);
        $result = $this->solr->searchWithFilters($keyword, $filters);
        $endTime = microtime(true);
        
        $responseTime = ($endTime - $startTime) * 1000;

        $docs = $result['response']['docs'] ?? [];
        $count = $result['response']['numFound'] ?? 0;

        return response()->json([
            'count' => $count,
            'data' => $docs,
            'response_time' => round($responseTime, 2),
            'engine' => 'solr',
            'filters_applied' => $filters
        ]);
    }

    /**
     * 📊 Lấy thống kê Solr
     */
    public function stats()
    {
        try {
            $coreInfo = $this->solr->getCoreInfo();
            $searchStats = $this->solr->getSearchStats();
            $ping = $this->solr->ping();

            return response()->json([
                'ping' => $ping,
                'core_info' => $coreInfo,
                'search_stats' => $searchStats,
                'engine' => 'solr'
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Solr stats error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to get Solr statistics',
                'engine' => 'solr'
            ], 500);
        }
    }

    /**
     * 🔄 Reindex tất cả listings vào Solr
     */
    public function reindex()
    {
        try {
            // Get all listings from database
            $listings = \App\Models\Listing::with(['category', 'seller'])
                ->where('status', 'active')
                ->get();

            $documents = [];
            foreach ($listings as $listing) {
                $documents[] = [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'description' => $listing->description,
                    'price' => (float) $listing->price,
                    'category_id' => $listing->category_id,
                    'condition_grade' => $listing->condition_grade ?? 'B',
                    'status' => $listing->status,
                    'seller_id' => $listing->seller_id,
                    'created_at' => $listing->created_at->toISOString(),
                    'updated_at' => $listing->updated_at->toISOString(),
                ];
            }

            $success = $this->solr->bulkIndex($documents);

            return response()->json([
                'success' => $success,
                'message' => $success ? "Reindexed {$listings->count()} listings successfully" : 'Reindex failed',
                'count' => $listings->count(),
                'engine' => 'solr'
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Solr reindex error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Reindex failed: ' . $e->getMessage(),
                'engine' => 'solr'
            ], 500);
        }
    }

    /**
     * 🧹 Clear toàn bộ Solr index
     */
    public function clearIndex()
    {
        try {
            $success = $this->solr->deleteByQuery('*:*');

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Solr index cleared successfully' : 'Failed to clear index',
                'engine' => 'solr'
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Solr clearIndex error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Clear index failed: ' . $e->getMessage(),
                'engine' => 'solr'
            ], 500);
        }
    }

    /**
     * 📝 Log search history vào Solr
     */
    private function logSearchHistory(string $keyword, int $userId, int $resultsCount, float $responseTime): bool
    {
        try {
            $searchHistoryDoc = [
                'id' => uniqid('search_'),
                'user_id' => $userId,
                'keyword' => $keyword,
                'timestamp' => now()->toISOString(),
                'results_count' => $resultsCount,
                'response_time' => $responseTime,
                'engine' => 'solr'
            ];

            return $this->solr->indexDocument($searchHistoryDoc['id'], $searchHistoryDoc);
        } catch (\Throwable $e) {
            Log::error('❌ Solr logSearchHistory failed: ' . $e->getMessage());
            return false;
        }
    }
}
