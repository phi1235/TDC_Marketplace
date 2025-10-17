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
        return response()->json([
            'count' => count($result['hits']['hits']),
            'data' => $result['hits']['hits'],
        ]);
    }
}
