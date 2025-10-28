<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticSearchService;
use App\Services\SolrService;
use Illuminate\Support\Facades\Log;

class CompareController extends Controller
{
    public function index(Request $request, ElasticSearchService $es, SolrService $solr)
    {
        $query = trim($request->get('q', ''));
        if (empty($query)) {
            return response()->json([
                'message' => 'Missing query parameter.',
                'elasticsearch' => [],
                'solr' => [],
            ], 400);
        }

        $startTime = microtime(true);

        //  Dùng query thông minh giống /search-es
        $smartQuery = [
            'query' => [
                'bool' => [
                    'should' => [
                        [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['title^3'],
                                'type' => 'bool_prefix',
                            ],
                        ],
                        [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['title^3'],
                                'fuzziness' => 'AUTO',
                                'prefix_length' => 1,
                                'minimum_should_match' => '70%',
                            ],
                        ],
                        [
                            'match_phrase_prefix' => [
                                'title' => [
                                    'query' => $query,
                                    'max_expansions' => 20,
                                ],
                            ],
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
            'size' => 30,
            '_source' => ['title', 'description', 'price', 'category_id', 'image'],
        ];

        //  Gọi customSearch() thay vì search()
        $esData = $this->measure(fn() => $es->customSearch('listings', $smartQuery));
        $solrData = $this->measure(fn() => $solr->search($query));

        $totalTime = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'query' => $query,
            'elasticsearch' => [
                'results' => $esData['results'] ?? ($esData['hits']['hits'] ?? []),
                'total' => $esData['total'] ?? ($esData['hits']['total']['value'] ?? 0),
                'time_ms' => $esData['time_ms'] ?? 0,
            ],
            'solr' => [
                'results' => $solrData['results'] ?? ($solrData['response']['docs'] ?? []),
                'total' => $solrData['total'] ?? ($solrData['response']['numFound'] ?? 0),
                'time_ms' => $solrData['time_ms'] ?? 0,
            ],
            'total_time_ms' => $totalTime,
        ]);
    }

    private function measure(callable $fn)
    {
        $start = microtime(true);
        $result = $fn();
        $time = round((microtime(true) - $start) * 1000, 2);
        $result['time_ms'] = $time;
        return $result;
    }
}
