<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SolrService;

class SolrController extends Controller
{
    public function index(Request $request, SolrService $solr)
    {
        $q = $request->get('q', '');
        if (empty($q)) {
            return response()->json([
                'query' => $q,
                'results' => [],
                'message' => 'No keyword provided'
            ]);
        }

        $res = $solr->search($q);

        return response()->json([
            'query' => $q,
            'results' => $res['response']['docs'] ?? [],
            'highlight' => $res['highlighting'] ?? [],
            'numFound' => $res['response']['numFound'] ?? 0,
        ]);
    }
}
