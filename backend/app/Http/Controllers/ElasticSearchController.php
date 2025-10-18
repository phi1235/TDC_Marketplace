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

 public function index(Request $request)
{
    $keyword = $request->get('q', '');

    if (empty($keyword)) {
        return response()->json([
            'count' => 0,
            'data' => [],
            'message' => 'No keyword provided'
        ]);
    }

    // ✅ Query nâng cao: tìm 1 ký tự, tiếng Việt, không phân biệt hoa/thường
    $query = [
        'query' => [
            'multi_match' => [
                'query' => $keyword,
            'fields' => ['name^3'], // ❗ chỉ tìm theo tên
                'type' => 'bool_prefix' // cho phép tìm 1 ký tự, ví dụ "a" -> "áo"
            ]
        ],
        'size' => 30
    ];

    // ✅ Gọi hàm customSearch() trong service
    $result = $this->search->customSearch('listings', $query);

    return response()->json([
        'count' => count($result['hits']['hits'] ?? []),
        'data' => $result['hits']['hits'] ?? [],
    ]);
}
}
