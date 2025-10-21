<?php

namespace App\Http\Controllers;

use App\Services\ElasticSearchService;
use Illuminate\Http\Request;

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
                    'must' => [
                        [
                            'multi_match' => [
                                'query' => $keyword,
                                'fields' => ['title^3'],
                                'operator' => 'and', // 🔒 bắt buộc có đủ từ
                                'fuzziness' => 'AUTO', // cho phép sai chính tả nhẹ
                                'minimum_should_match' => '80%' // cho phép lệch 20%
                            ]
                        ]
                    ]
                ]
            ],
            'size' => 30
        ];

        $result = $this->search->customSearch('listings', $query);

        return response()->json([
            'count' => count($result['hits']['hits'] ?? []),
            'data' => $result['hits']['hits'] ?? [],
        ]);
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

        // ✅ Query nhanh, tìm 1 phần đầu, fuzzy nhẹ
        $query = [
            'query' => [
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => ['title^3'],
                    'type' => 'phrase_prefix'
                ]
            ],
            '_source' => ['title'], // chỉ cần title cho nhanh
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
}
