<?php

namespace App\Http\Controllers;

use App\Services\ElasticSearchService;
use App\Services\SolrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DualSearchController extends Controller
{
    protected $elasticSearchService;
    protected $solrService;

    public function __construct(ElasticSearchService $elasticSearchService, SolrService $solrService)
    {
        $this->elasticSearchService = $elasticSearchService;
        $this->solrService = $solrService;
    }

    public function search(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json([
                'success' => false,
                'message' => 'No keyword provided'
            ]);
        }

        try {
            // Search both engines simultaneously
            $startTime = microtime(true);
            
            // Elasticsearch search
            $esStart = microtime(true);
            $esResult = $this->elasticSearchService->search('listings', $keyword);
            $esTime = (microtime(true) - $esStart) * 1000; // Convert to ms
            
            // Solr search
            $solrStart = microtime(true);
            $solrResult = $this->solrService->search($keyword);
            $solrTime = (microtime(true) - $solrStart) * 1000; // Convert to ms
            
            $totalTime = (microtime(true) - $startTime) * 1000;

            return response()->json([
                'success' => true,
                'keyword' => $keyword,
                'total_time' => round($totalTime, 2),
                'results' => [
                    'elasticsearch' => [
                        'data' => $esResult['hits']['hits'] ?? [],
                        'total' => $esResult['hits']['total']['value'] ?? 0,
                        'response_time' => round($esTime, 2)
                    ],
                    'solr' => [
                        'data' => $solrResult['response']['docs'] ?? [],
                        'total' => $solrResult['response']['numFound'] ?? 0,
                        'response_time' => round($solrTime, 2)
                    ]
                ],
                'comparison' => [
                    'faster_engine' => $esTime < $solrTime ? 'elasticsearch' : 'solr',
                    'time_difference' => round(abs($esTime - $solrTime), 2),
                    'es_time' => round($esTime, 2),
                    'solr_time' => round($solrTime, 2)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Dual search failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ping()
    {
        try {
            $esStatus = $this->elasticSearchService->ping();
            $solrStatus = $this->solrService->ping();
            
            return response()->json([
                'success' => true,
                'engines' => [
                    'elasticsearch' => $esStatus,
                    'solr' => $solrStatus
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ping failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
