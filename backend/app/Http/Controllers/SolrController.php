<?php

namespace App\Http\Controllers;

use App\Services\SolrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolrController extends Controller
{
    public function __construct(protected SolrService $solr)
    {
    }

    /**
     * Tìm kiếm listings qua Solr (query tương tự Elasticsearch smart search)
     */
    public function index(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'message' => 'No keyword provided',
            ]);
        }

        $result = $this->solr->smartSearch($keyword);
        $docs = $result['response']['docs'] ?? [];
        $count = (int) ($result['response']['numFound'] ?? count($docs));

        try {
            $this->solr->logSearch($keyword, $count, auth()->id());
        } catch (\Throwable $e) {
            Log::error('Solr search history log failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'query' => $keyword,
            'count' => $count,
            'data' => $docs,
            'highlight' => $result['highlighting'] ?? [],
            'numFound' => $count,
        ]);
    }

    /**
     * Autocomplete realtime tương tự /search-es/suggest
     */
    public function suggestions(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = $this->solr->suggest($keyword, 10);
        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * Lấy lịch sử tìm kiếm của user từ Solr history core
     */
    public function history()
    {
        $userId = auth()->id() ?? 0;
        $history = $this->solr->getSearchHistory($userId);
        return response()->json(['history' => $history]);
    }

    /**
     * Xóa lịch sử tìm kiếm của user hiện tại
     */
    public function clearHistory()
    {
        try {
            $userId = auth()->id() ?? 0;
            $this->solr->clearSearchHistoryByUser($userId);

            return response()->json([
                'success' => true,
                'message' => 'History cleared successfully!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Solr clearHistory error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error clearing history: ' . $e->getMessage(),
            ], 500);
        }
    }
}
