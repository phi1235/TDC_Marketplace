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

        $smartQuery = [
            'query' => [
                'bool' => [
                    'should' => [

                        // PREFIX MATCH
                        [
                            'match_phrase_prefix' => [
                                'title' => [
                                    'query' => $query,
                                    'boost' => 5,
                                ]
                            ]
                        ],

                        // LIGHT FUZZY (fuzziness = 1)
                        [
                            'match' => [
                                'title' => [
                                    'query' => $query,
                                    'boost' => 4,
                                    'fuzziness' => 1,
                                    'prefix_length' => 1,
                                ]
                            ]
                        ],

                        // DESCRIPTION PREFIX
                        [
                            'wildcard' => [
                                'description' => [
                                    'value' => "{$query}*",
                                    'boost' => 2,
                                ]
                            ]
                        ],

                        // DESCRIPTION LIGHT FUZZY
                        [
                            'match' => [
                                'description' => [
                                    'query' => $query,
                                    'fuzziness' => 1,
                                    'boost' => 1,
                                ]
                            ]
                        ],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],

            'min_score' => 1,

            '_source' => ['title', 'description', 'price', 'category_id', 'image'],
            'size' => 30,
        ];

        $esData   = $this->measure(fn() => $es->customSearch('listings', $smartQuery));
        $solrData = $this->measure(fn() => $solr->smartSearch($query));

        $solrDocsRaw = $solrData['response']['docs'] ?? [];

        $solrDocs = collect($solrDocsRaw)
            ->filter(fn($d) => ($d['score'] ?? 0) >= 2.0)
            ->values()
            ->toArray();

        return response()->json([
            'query' => $query,
            'elasticsearch' => [
                'results' => $esData['hits']['hits'] ?? [],
                'total'   => $esData['hits']['total']['value'] ?? 0,
                'time_ms' => $esData['time_ms'] ?? 0,
            ],
            'solr' => [
                'results' => $solrDocs,
                'total'   => count($solrDocs),
                'time_ms' => $solrData['time_ms'] ?? 0,
            ],
        ]);
    }

    private function measure(callable $fn)
    {
        $start = microtime(true);
        $result = $fn();
        $result['time_ms'] = round((microtime(true) - $start) * 1000, 2);
        return $result;
    }
}
