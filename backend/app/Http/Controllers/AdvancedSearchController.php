<?php

namespace App\Http\Controllers;

use App\Services\UnifiedSearchService;
use App\Services\ElasticSearchService;
use App\Services\SolrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdvancedSearchController extends Controller
{
    protected $unifiedSearch;
    protected $elasticSearch;
    protected $solr;

    public function __construct(
        UnifiedSearchService $unifiedSearch,
        ElasticSearchService $elasticSearch,
        SolrService $solr
    ) {
        $this->unifiedSearch = $unifiedSearch;
        $this->elasticSearch = $elasticSearch;
        $this->solr = $solr;
    }

    /**
     * 🔍 Faceted search
     */
    public function facetedSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'filters' => 'array',
            'filters.category' => 'array',
            'filters.price_min' => 'numeric|min:0',
            'filters.price_max' => 'numeric|min:0',
            'filters.location' => 'string',
            'filters.condition' => 'string|in:new,used,refurbished',
            'engine' => 'required|in:elasticsearch,solr,both'
        ]);

        $query = $request->query;
        $filters = $request->filters ?? [];
        $engine = $request->engine;

        $results = [];

        if ($engine === 'elasticsearch' || $engine === 'both') {
            $results['elasticsearch'] = $this->performFacetedSearch('elasticsearch', $query, $filters);
        }

        if ($engine === 'solr' || $engine === 'both') {
            $results['solr'] = $this->performFacetedSearch('solr', $query, $filters);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'filters_applied' => $filters
        ]);
    }

    /**
     * 🔤 Auto-complete suggestions
     */
    public function getSuggestions(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:100',
            'engine' => 'required|in:elasticsearch,solr,both',
            'limit' => 'integer|min:1|max:20'
        ]);

        $query = $request->query;
        $engine = $request->engine;
        $limit = $request->get('limit', 10);

        $suggestions = [];

        if ($engine === 'elasticsearch' || $engine === 'both') {
            $suggestions['elasticsearch'] = $this->getEngineSuggestions('elasticsearch', $query, $limit);
        }

        if ($engine === 'solr' || $engine === 'both') {
            $suggestions['solr'] = $this->getEngineSuggestions('solr', $query, $limit);
        }

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * 🌍 Geospatial search
     */
    public function geospatialSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.1|max:100', // km
            'engine' => 'required|in:elasticsearch,solr,both'
        ]);

        $query = $request->query;
        $lat = $request->latitude;
        $lng = $request->longitude;
        $radius = $request->radius;
        $engine = $request->engine;

        $results = [];

        if ($engine === 'elasticsearch' || $engine === 'both') {
            $results['elasticsearch'] = $this->performGeospatialSearch('elasticsearch', $query, $lat, $lng, $radius);
        }

        if ($engine === 'solr' || $engine === 'both') {
            $results['solr'] = $this->performGeospatialSearch('solr', $query, $lat, $lng, $radius);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'location' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'radius' => $radius
            ]
        ]);
    }

    /**
     * 🔍 Fuzzy search
     */
    public function fuzzySearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'fuzziness' => 'integer|min:0|max:2',
            'engine' => 'required|in:elasticsearch,solr,both'
        ]);

        $query = $request->query;
        $fuzziness = $request->get('fuzziness', 1);
        $engine = $request->engine;

        $results = [];

        if ($engine === 'elasticsearch' || $engine === 'both') {
            $results['elasticsearch'] = $this->performFuzzySearch('elasticsearch', $query, $fuzziness);
        }

        if ($engine === 'solr' || $engine === 'both') {
            $results['solr'] = $this->performFuzzySearch('solr', $query, $fuzziness);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'fuzziness' => $fuzziness
        ]);
    }

    /**
     * 📊 Search analytics
     */
    public function getSearchAnalytics(Request $request)
    {
        $request->validate([
            'period' => 'string|in:1h,24h,7d,30d',
            'engine' => 'string|in:elasticsearch,solr,both'
        ]);

        $period = $request->get('period', '24h');
        $engine = $request->get('engine', 'both');

        $analytics = [
            'total_searches' => $this->getTotalSearches($period, $engine),
            'popular_keywords' => $this->getPopularKeywords($period, $engine),
            'search_trends' => $this->getSearchTrends($period, $engine),
            'engine_performance' => $this->getEnginePerformance($period, $engine),
            'user_behavior' => $this->getUserBehavior($period, $engine)
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'period' => $period
        ]);
    }

    /**
     * Perform faceted search
     */
    private function performFacetedSearch(string $engine, string $query, array $filters): array
    {
        $startTime = microtime(true);

        try {
            if ($engine === 'elasticsearch') {
                $searchBody = [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'multi_match' => [
                                        'query' => $query,
                                        'fields' => ['title^2', 'description']
                                    ]
                                ]
                            ],
                            'filter' => $this->buildElasticsearchFilters($filters)
                        ]
                    ],
                    'aggs' => [
                        'categories' => [
                            'terms' => ['field' => 'category_id']
                        ],
                        'price_ranges' => [
                            'histogram' => [
                                'field' => 'price',
                                'interval' => 1000000
                            ]
                        ]
                    ]
                ];

                $response = $this->elasticSearch->search('listings', $searchBody);
            } else {
                $solrQuery = $this->buildSolrQuery($query, $filters);
                $response = $this->solr->search($solrQuery);
            }

            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'results' => $response,
                'response_time' => round($responseTime, 2),
                'total_found' => $this->extractTotalFound($response, $engine),
                'facets' => $this->extractFacets($response, $engine)
            ];

        } catch (\Throwable $e) {
            Log::error("Faceted search failed for {$engine}: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'response_time' => 0,
                'total_found' => 0,
                'facets' => []
            ];
        }
    }

    /**
     * Get engine suggestions
     */
    private function getEngineSuggestions(string $engine, string $query, int $limit): array
    {
        $startTime = microtime(true);

        try {
            if ($engine === 'elasticsearch') {
                $suggestBody = [
                    'suggest' => [
                        'title_suggest' => [
                            'prefix' => $query,
                            'completion' => [
                                'field' => 'title_suggest',
                                'size' => $limit
                            ]
                        ]
                    ]
                ];

                $response = $this->elasticSearch->search('listings', $suggestBody);
                $suggestions = $response['suggest']['title_suggest'][0]['options'] ?? [];
            } else {
                $response = $this->solr->suggest($query, $limit);
                $suggestions = $response['suggest']['mySuggester'][$query]['suggestions'] ?? [];
            }

            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'suggestions' => $suggestions,
                'response_time' => round($responseTime, 2),
                'count' => count($suggestions)
            ];

        } catch (\Throwable $e) {
            Log::error("Suggestions failed for {$engine}: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'suggestions' => [],
                'response_time' => 0,
                'count' => 0
            ];
        }
    }

    /**
     * Perform geospatial search
     */
    private function performGeospatialSearch(string $engine, string $query, float $lat, float $lng, float $radius): array
    {
        $startTime = microtime(true);

        try {
            if ($engine === 'elasticsearch') {
                $searchBody = [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'multi_match' => [
                                        'query' => $query,
                                        'fields' => ['title^2', 'description']
                                    ]
                                ]
                            ],
                            'filter' => [
                                [
                                    'geo_distance' => [
                                        'distance' => $radius . 'km',
                                        'location' => [
                                            'lat' => $lat,
                                            'lon' => $lng
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'sort' => [
                        '_geo_distance' => [
                            'location' => [
                                'lat' => $lat,
                                'lon' => $lng
                            ],
                            'order' => 'asc',
                            'unit' => 'km'
                        ]
                    ]
                ];

                $response = $this->elasticSearch->search('listings', $searchBody);
            } else {
                $solrQuery = $this->buildSolrGeospatialQuery($query, $lat, $lng, $radius);
                $response = $this->solr->search($solrQuery);
            }

            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'results' => $response,
                'response_time' => round($responseTime, 2),
                'total_found' => $this->extractTotalFound($response, $engine)
            ];

        } catch (\Throwable $e) {
            Log::error("Geospatial search failed for {$engine}: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'response_time' => 0,
                'total_found' => 0
            ];
        }
    }

    /**
     * Perform fuzzy search
     */
    private function performFuzzySearch(string $engine, string $query, int $fuzziness): array
    {
        $startTime = microtime(true);

        try {
            if ($engine === 'elasticsearch') {
                $searchBody = [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title^2', 'description'],
                            'fuzziness' => $fuzziness,
                            'type' => 'best_fields'
                        ]
                    ]
                ];

                $response = $this->elasticSearch->search('listings', $searchBody);
            } else {
                $solrQuery = $this->buildSolrFuzzyQuery($query, $fuzziness);
                $response = $this->solr->search($solrQuery);
            }

            $responseTime = (microtime(true) - $startTime) * 1000;

            return [
                'results' => $response,
                'response_time' => round($responseTime, 2),
                'total_found' => $this->extractTotalFound($response, $engine)
            ];

        } catch (\Throwable $e) {
            Log::error("Fuzzy search failed for {$engine}: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'response_time' => 0,
                'total_found' => 0
            ];
        }
    }

    /**
     * Helper methods
     */
    private function buildElasticsearchFilters(array $filters): array
    {
        $esFilters = [];

        if (isset($filters['category']) && !empty($filters['category'])) {
            $esFilters[] = ['terms' => ['category_id' => $filters['category']]];
        }

        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $range = [];
            if (isset($filters['price_min'])) $range['gte'] = $filters['price_min'];
            if (isset($filters['price_max'])) $range['lte'] = $filters['price_max'];
            $esFilters[] = ['range' => ['price' => $range]];
        }

        if (isset($filters['condition'])) {
            $esFilters[] = ['term' => ['condition' => $filters['condition']]];
        }

        return $esFilters;
    }

    private function buildSolrQuery(string $query, array $filters): string
    {
        $solrQuery = $query;

        if (isset($filters['category']) && !empty($filters['category'])) {
            $categories = implode(' OR ', $filters['category']);
            $solrQuery .= " AND category_id:({$categories})";
        }

        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $priceRange = [];
            if (isset($filters['price_min'])) $priceRange[] = $filters['price_min'];
            if (isset($filters['price_max'])) $priceRange[] = $filters['price_max'];
            $solrQuery .= " AND price:[{$priceRange[0]} TO {$priceRange[1]}]";
        }

        return $solrQuery;
    }

    private function extractTotalFound(array $response, string $engine): int
    {
        if ($engine === 'elasticsearch') {
            return $response['hits']['total']['value'] ?? 0;
        } else {
            return $response['response']['numFound'] ?? 0;
        }
    }

    private function extractFacets(array $response, string $engine): array
    {
        if ($engine === 'elasticsearch') {
            return $response['aggregations'] ?? [];
        } else {
            return $response['facet_counts'] ?? [];
        }
    }

    // Placeholder methods for analytics
    private function getTotalSearches(string $period, string $engine): int
    {
        return rand(100, 1000);
    }

    private function getPopularKeywords(string $period, string $engine): array
    {
        return [
            ['term' => 'iPhone 15', 'count' => 45],
            ['term' => 'MacBook Pro', 'count' => 32],
            ['term' => 'sách giáo khoa', 'count' => 28]
        ];
    }

    private function getSearchTrends(string $period, string $engine): array
    {
        return [
            'labels' => ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
            'searches' => [12, 8, 25, 45, 38, 22]
        ];
    }

    private function getEnginePerformance(string $period, string $engine): array
    {
        return [
            'elasticsearch' => ['avg_response_time' => 29, 'success_rate' => 98.5],
            'solr' => ['avg_response_time' => 27, 'success_rate' => 99.2]
        ];
    }

    private function getUserBehavior(string $period, string $engine): array
    {
        return [
            'unique_users' => 150,
            'avg_searches_per_user' => 3.2,
            'bounce_rate' => 15.5
        ];
    }
}
