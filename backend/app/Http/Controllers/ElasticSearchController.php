<?php

namespace App\Http\Controllers;

use App\Services\ElasticSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ElasticSearchController extends Controller
{
    protected $search;

    public function __construct(ElasticSearchService $search)
    {
        $this->search = $search;
    }

    /**
     * 🔍 Tìm kiếm chính (nhấn Enter)
     */
    public function index(Request $request)
    {
        $keyword = trim($request->get('q', ''));

        if (empty($keyword)) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'message' => 'No keyword provided'
            ]);
        }

        // ✅ Query chính xác hơn (tất cả từ khóa phải có mặt)
        $query = [
            'query' => [
                'bool' => [
                    'must' => [[
                        'multi_match' => [
                            'query' => $keyword,
                            'fields' => ['title^3'],
                            'operator' => 'and',
                            'fuzziness' => 'AUTO',
                            'minimum_should_match' => '80%',
                        ]
                    ]]
                ]
            ],
            'size' => 30
        ];

        $result = $this->search->customSearch('listings', $query);
        $hits   = $result['hits']['hits'] ?? [];
        $count  = count($hits);

        /**
         * 🧠 Ghi lại lịch sử tìm kiếm theo user
         */
        try {
            $userId = auth()->id() ?? 0;
            $es = new \App\Services\ElasticSearchService();

            $es->indexDocument('search_history', uniqid(), [
                'keyword'       => $keyword,
                'user_id'       => $userId,
                'timestamp'     => now()->toISOString(),
                'results_count' => $count,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Save search history failed: ' . $e->getMessage());
        }

        return response()->json([
            'count' => $count,
            'data'  => $hits,
        ]);
    }

    /**
     * 📜 Trả về 10 từ khoá user đã tìm gần đây (theo timestamp desc)
     */
    public function history(Request $request)
    {
        $userId = auth()->id() ?? 0;

        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['user_id' => $userId]]
                    ]
                ]
            ],
            'sort' => [
                ['timestamp' => ['order' => 'desc']]
            ],
            '_source' => ['keyword', 'timestamp', 'results_count'],
            'size' => 10
        ];

        $res = $this->search->customSearch('search_history', $query);

        $history = collect($res['hits']['hits'] ?? [])
            ->pluck('_source')
            ->map(fn($h) => [
                'keyword'       => $h['keyword'] ?? '',
                'timestamp'     => $h['timestamp'] ?? null,
                'results_count' => $h['results_count'] ?? 0,
            ])
            ->values();

        return response()->json(['history' => $history]);
    }

    /**
     * 💡 Gợi ý realtime (dropdown như Google)
     */
    public function suggestions(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json(['suggestions' => []]);
        }

        $query = [
            'query' => [
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => ['title^3'],
                    'type' => 'phrase_prefix'
                ]
            ],
            '_source' => ['title'],
            'size' => 10
        ];

        $result = $this->search->customSearch('listings', $query);

        $suggestions = collect($result['hits']['hits'] ?? [])
            ->pluck('_source.title')
            ->filter()
            ->unique()
            ->values();

        return response()->json([
            'suggestions' => $suggestions
        ]);
    }

    /**
     * 🧹 Xoá lịch sử tìm kiếm của user hiện tại
     */
    public function clearHistory()
    {
        try {
            $userId = auth()->id() ?? 0;
            $es = new \App\Services\ElasticSearchService();

            $response = $es->deleteByQuery('search_history', [
                'bool' => [
                    'must' => [
                        ['term' => ['user_id' => $userId]]
                    ]
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'History cleared successfully!',
                'response' => $response
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ clearHistory error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing history: ' . $e->getMessage()
            ], 500);
        }
    }
}
