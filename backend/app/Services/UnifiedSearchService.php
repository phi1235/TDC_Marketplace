<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class UnifiedSearchService
{
    protected $elasticsearch;
    protected $solr;

    public function __construct(ElasticSearchService $elasticsearch, SolrService $solr)
    {
        $this->elasticsearch = $elasticsearch;
        $this->solr = $solr;
    }

    /**
     * 🔍 Tìm kiếm song song trên cả Elasticsearch và Solr
     */
    public function searchBoth(string $keyword, array $filters = []): array
    {
        $startTime = microtime(true);
        
        // Prepare search parameters
        $esQuery = $this->buildElasticsearchQuery($keyword, $filters);
        $solrQuery = $this->buildSolrQuery($keyword, $filters);

        // Execute searches in parallel
        $esStartTime = microtime(true);
        $esResult = $this->elasticsearch->customSearch('listings', $esQuery);
        $esEndTime = microtime(true);
        $esResponseTime = ($esEndTime - $esStartTime) * 1000;

        $solrStartTime = microtime(true);
        $solrResult = $this->solr->customSearch($solrQuery);
        $solrEndTime = microtime(true);
        $solrResponseTime = ($solrEndTime - $solrStartTime) * 1000;

        $totalTime = (microtime(true) - $startTime) * 1000;

        // Process results
        $esHits = $esResult['hits']['hits'] ?? [];
        $solrDocs = $solrResult['response']['docs'] ?? [];

        return [
            'keyword' => $keyword,
            'filters' => $filters,
            'total_time' => round($totalTime, 2),
            'elasticsearch' => [
                'results' => $esHits,
                'count' => count($esHits),
                'response_time' => round($esResponseTime, 2),
                'status' => 'success'
            ],
            'solr' => [
                'results' => $solrDocs,
                'count' => count($solrDocs),
                'response_time' => round($solrResponseTime, 2),
                'status' => 'success'
            ],
            'comparison' => [
                'faster_engine' => $esResponseTime < $solrResponseTime ? 'elasticsearch' : 'solr',
                'time_difference' => abs($esResponseTime - $solrResponseTime),
                'results_difference' => abs(count($esHits) - count($solrDocs))
            ]
        ];
    }

    /**
     * 📊 So sánh performance metrics
     */
    public function comparePerformance(string $keyword, int $iterations = 10): array
    {
        $esTimes = [];
        $solrTimes = [];
        $esResults = [];
        $solrResults = [];

        for ($i = 0; $i < $iterations; $i++) {
            // Test Elasticsearch
            $esStart = microtime(true);
            $esQuery = $this->buildElasticsearchQuery($keyword);
            $esResult = $this->elasticsearch->customSearch('listings', $esQuery);
            $esEnd = microtime(true);
            $esTimes[] = ($esEnd - $esStart) * 1000;
            $esResults[] = count($esResult['hits']['hits'] ?? []);

            // Test Solr
            $solrStart = microtime(true);
            $solrQuery = $this->buildSolrQuery($keyword);
            $solrResult = $this->solr->customSearch($solrQuery);
            $solrEnd = microtime(true);
            $solrTimes[] = ($solrEnd - $solrStart) * 1000;
            $solrResults[] = count($solrResult['response']['docs'] ?? []);
        }

        return [
            'keyword' => $keyword,
            'iterations' => $iterations,
            'elasticsearch' => [
                'avg_response_time' => round(array_sum($esTimes) / count($esTimes), 2),
                'min_response_time' => round(min($esTimes), 2),
                'max_response_time' => round(max($esTimes), 2),
                'avg_results' => round(array_sum($esResults) / count($esResults), 2),
                'consistency' => $this->calculateConsistency($esTimes)
            ],
            'solr' => [
                'avg_response_time' => round(array_sum($solrTimes) / count($solrTimes), 2),
                'min_response_time' => round(min($solrTimes), 2),
                'max_response_time' => round(max($solrTimes), 2),
                'avg_results' => round(array_sum($solrResults) / count($solrResults), 2),
                'consistency' => $this->calculateConsistency($solrTimes)
            ],
            'winner' => [
                'faster' => array_sum($esTimes) < array_sum($solrTimes) ? 'elasticsearch' : 'solr',
                'more_consistent' => $this->calculateConsistency($esTimes) > $this->calculateConsistency($solrTimes) ? 'elasticsearch' : 'solr'
            ]
        ];
    }

    /**
     * 🧪 Test Vietnamese language handling
     */
    public function testVietnameseSupport(): array
    {
        $testCases = [
            'sách giáo khoa' => 'Vietnamese with diacritics',
            'sach giao khoa' => 'Vietnamese without diacritics',
            'sách toán lớp 10' => 'Complex Vietnamese phrase',
            'sach toan lop 10' => 'Complex Vietnamese without diacritics',
            'điện thoại' => 'Vietnamese with special characters',
            'dien thoai' => 'Vietnamese without special characters'
        ];

        $results = [];

        foreach ($testCases as $query => $description) {
            $esStart = microtime(true);
            $esQuery = $this->buildElasticsearchQuery($query);
            $esResult = $this->elasticsearch->customSearch('listings', $esQuery);
            $esEnd = microtime(true);
            $esTime = ($esEnd - $esStart) * 1000;

            $solrStart = microtime(true);
            $solrQuery = $this->buildSolrQuery($query);
            $solrResult = $this->solr->customSearch($solrQuery);
            $solrEnd = microtime(true);
            $solrTime = ($solrEnd - $solrStart) * 1000;

            $results[] = [
                'query' => $query,
                'description' => $description,
                'elasticsearch' => [
                    'results_count' => count($esResult['hits']['hits'] ?? []),
                    'response_time' => round($esTime, 2),
                    'top_results' => array_slice($esResult['hits']['hits'] ?? [], 0, 3)
                ],
                'solr' => [
                    'results_count' => count($solrResult['response']['docs'] ?? []),
                    'response_time' => round($solrTime, 2),
                    'top_results' => array_slice($solrResult['response']['docs'] ?? [], 0, 3)
                ]
            ];
        }

        return $results;
    }

    /**
     * 🔄 Test indexing performance
     */
    public function testIndexingPerformance(array $documents): array
    {
        // Test Elasticsearch indexing
        $esStart = microtime(true);
        $esSuccess = true;
        foreach ($documents as $doc) {
            if (!$this->elasticsearch->indexDocument('listings', $doc['id'], $doc)) {
                $esSuccess = false;
                break;
            }
        }
        $esEnd = microtime(true);
        $esTime = ($esEnd - $esStart) * 1000;

        // Test Solr indexing
        $solrStart = microtime(true);
        $solrSuccess = $this->solr->bulkIndex($documents);
        $solrEnd = microtime(true);
        $solrTime = ($solrEnd - $solrStart) * 1000;

        return [
            'document_count' => count($documents),
            'elasticsearch' => [
                'success' => $esSuccess,
                'total_time' => round($esTime, 2),
                'avg_time_per_doc' => round($esTime / count($documents), 2),
                'docs_per_second' => round(count($documents) / ($esTime / 1000), 2)
            ],
            'solr' => [
                'success' => $solrSuccess,
                'total_time' => round($solrTime, 2),
                'avg_time_per_doc' => round($solrTime / count($documents), 2),
                'docs_per_second' => round(count($documents) / ($solrTime / 1000), 2)
            ],
            'winner' => $esTime < $solrTime ? 'elasticsearch' : 'solr'
        ];
    }

    /**
     * 🏥 Health check cả hai engines
     */
    public function healthCheck(): array
    {
        $esHealth = $this->elasticsearch->ping();
        $solrHealth = $this->solr->ping();

        return [
            'elasticsearch' => [
                'status' => $esHealth ? 'healthy' : 'unhealthy',
                'ping' => $esHealth
            ],
            'solr' => [
                'status' => $solrHealth ? 'healthy' : 'unhealthy',
                'ping' => $solrHealth
            ],
            'overall_status' => ($esHealth && $solrHealth) ? 'healthy' : 'degraded'
        ];
    }

    /**
     * 📈 Lấy resource usage metrics
     */
    public function getResourceMetrics(): array
    {
        // This would typically integrate with Docker stats or system monitoring
        // For now, we'll return placeholder data structure
        return [
            'elasticsearch' => [
                'memory_usage' => 'N/A - requires Docker stats integration',
                'cpu_usage' => 'N/A - requires Docker stats integration',
                'disk_usage' => 'N/A - requires Docker stats integration'
            ],
            'solr' => [
                'memory_usage' => 'N/A - requires Docker stats integration',
                'cpu_usage' => 'N/A - requires Docker stats integration',
                'disk_usage' => 'N/A - requires Docker stats integration'
            ]
        ];
    }

    /**
     * 🔧 Build Elasticsearch query
     */
    private function buildElasticsearchQuery(string $keyword, array $filters = []): array
    {
        $query = [
            'query' => [
                'bool' => [
                    'must' => [[
                        'multi_match' => [
                            'query' => $keyword,
                            'fields' => ['title^3', 'description'],
                            'operator' => 'and',
                            'fuzziness' => 'AUTO',
                            'minimum_should_match' => '80%',
                        ]
                    ]]
                ]
            ],
            'size' => 30
        ];

        // Add filters
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    $query['query']['bool']['must'][] = [
                        'terms' => [$field => $value]
                    ];
                } else {
                    $query['query']['bool']['must'][] = [
                        'term' => [$field => $value]
                    ];
                }
            }
        }

        return $query;
    }

    /**
     * 🔧 Build Solr query
     */
    private function buildSolrQuery(string $keyword, array $filters = []): array
    {
        $query = [
            'q' => $keyword,
            'defType' => 'edismax',
            'qf' => 'title^3 description^1',
            'pf' => 'title^3 description^1',
            'mm' => '80%',
            'rows' => 30,
            'start' => 0,
            'wt' => 'json'
        ];

        // Add filters
        if (!empty($filters)) {
            $fq = [];
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    $fq[] = $field . ':(' . implode(' OR ', $value) . ')';
                } else {
                    $fq[] = $field . ':' . $value;
                }
            }
            $query['fq'] = $fq;
        }

        return $query;
    }

    /**
     * 📊 Calculate consistency (lower is better)
     */
    private function calculateConsistency(array $times): float
    {
        if (count($times) < 2) return 0;
        
        $mean = array_sum($times) / count($times);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $times)) / count($times);
        
        return round(sqrt($variance), 2);
    }
}
