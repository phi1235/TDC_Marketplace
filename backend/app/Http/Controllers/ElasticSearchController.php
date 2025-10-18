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
        $result = $this->search->search('listings', $keyword);
        
        // Kiểm tra nếu có lỗi hoặc không có kết quả
        if (!isset($result['hits']) || !isset($result['hits']['hits'])) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'error' => 'Search service unavailable'
            ]);
        }
        
        return response()->json([
            'count' => count($result['hits']['hits']),
            'data' => $result['hits']['hits'],
        ]);
    }
}
