<?php

namespace App\Services;

use App\Models\BenchmarkResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchBenchmarkService
{
    protected $unifiedSearch;
    protected $testDataGenerator;
    protected $performanceMonitor;

    public function __construct(
        UnifiedSearchService $unifiedSearch,
        TestDataGenerator $testDataGenerator,
        PerformanceMonitor $performanceMonitor
    ) {
        $this->unifiedSearch = $unifiedSearch;
        $this->testDataGenerator = $testDataGenerator;
        $this->performanceMonitor = $performanceMonitor;
    }

    /**
     * 🏃‍♂️ Chạy benchmark test đầy đủ
     */
    public function runFullBenchmark(array $options = []): array
    {
        $results = [];
        
        try {
            // 1. Search Performance Tests
            $results['search_performance'] = $this->runSearchPerformanceTests($options);
            
            // 2. Indexing Performance Tests
            $results['indexing_performance'] = $this->runIndexingPerformanceTests($options);
            
            // 3. Vietnamese Language Tests
            $results['vietnamese_tests'] = $this->runVietnameseLanguageTests($options);
            
            // 4. Load Tests
            $results['load_tests'] = $this->runLoadTests($options);
            
            // 5. Resource Usage Tests
            $results['resource_tests'] = $this->runResourceUsageTests($options);
            
            // 6. Generate Summary
            $results['summary'] = $this->generateBenchmarkSummary($results);
            
            return $results;
            
        } catch (\Throwable $e) {
            Log::error('❌ Benchmark failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 🔍 Test search performance
     */
    public function runSearchPerformanceTests(array $options = []): array
    {
        $iterations = $options['iterations'] ?? 10;
        $testQueries = $this->testDataGenerator->generatePerformanceTestQueries();
        $results = [];

        foreach (['elasticsearch', 'solr'] as $engine) {
            $engineResults = [];
            
            foreach ($testQueries as $query => $description) {
                $times = [];
                $resultCounts = [];
                
                for ($i = 0; $i < $iterations; $i++) {
                    $startTime = microtime(true);
                    $result = $this->unifiedSearch->searchBoth($query);
                    $endTime = microtime(true);
                    
                    $responseTime = ($endTime - $startTime) * 1000;
                    $times[] = $responseTime;
                    $resultCounts[] = $result[$engine]['count'];
                }
                
                // Calculate statistics
                $stats = $this->calculateStatistics($times);
                
                // Save to database
                $benchmarkResult = BenchmarkResult::create([
                    'engine' => $engine,
                    'test_type' => 'search_performance',
                    'test_name' => "Search: {$description}",
                    'dataset_size' => 0,
                    'iterations' => $iterations,
                    'response_time_avg' => $stats['avg'],
                    'response_time_min' => $stats['min'],
                    'response_time_max' => $stats['max'],
                    'response_time_p50' => $stats['p50'],
                    'response_time_p95' => $stats['p95'],
                    'response_time_p99' => $stats['p99'],
                    'throughput' => $stats['throughput'],
                    'successful_queries' => $iterations,
                    'failed_queries' => 0,
                    'success_rate' => 100.0,
                    'results_count' => round(array_sum($resultCounts) / count($resultCounts)),
                    'test_parameters' => json_encode([
                        'query' => $query,
                        'description' => $description,
                        'iterations' => $iterations
                    ]),
                    'test_started_at' => now(),
                    'test_completed_at' => now()
                ]);
                
                $engineResults[$query] = [
                    'description' => $description,
                    'statistics' => $stats,
                    'benchmark_id' => $benchmarkResult->id
                ];
            }
            
            $results[$engine] = $engineResults;
        }

        return $results;
    }

    /**
     * 📝 Test indexing performance
     */
    public function runIndexingPerformanceTests(array $options = []): array
    {
        $datasetSizes = $options['dataset_sizes'] ?? [100, 1000, 10000];
        $results = [];

        foreach ($datasetSizes as $size) {
            // Generate test data
            $listings = $this->testDataGenerator->generateListings($size);
            
            foreach (['elasticsearch', 'solr'] as $engine) {
                $startTime = microtime(true);
                
                try {
                    if ($engine === 'elasticsearch') {
                        $success = $this->indexToElasticsearch($listings);
                    } else {
                        $success = $this->indexToSolr($listings);
                    }
                    
                    $endTime = microtime(true);
                    $totalTime = ($endTime - $startTime) * 1000;
                    
                    // Save benchmark result
                    $benchmarkResult = BenchmarkResult::create([
                        'engine' => $engine,
                        'test_type' => 'indexing_performance',
                        'test_name' => "Indexing {$size} documents",
                        'dataset_size' => $size,
                        'iterations' => 1,
                        'response_time_avg' => $totalTime,
                        'throughput' => $size / ($totalTime / 1000),
                        'successful_queries' => $success ? 1 : 0,
                        'failed_queries' => $success ? 0 : 1,
                        'success_rate' => $success ? 100.0 : 0.0,
                        'test_parameters' => json_encode([
                            'dataset_size' => $size,
                            'document_type' => 'listings'
                        ]),
                        'test_started_at' => now(),
                        'test_completed_at' => now()
                    ]);
                    
                    $results["{$engine}_{$size}"] = [
                        'dataset_size' => $size,
                        'total_time' => $totalTime,
                        'throughput' => $size / ($totalTime / 1000),
                        'success' => $success,
                        'benchmark_id' => $benchmarkResult->id
                    ];
                    
                } catch (\Throwable $e) {
                    Log::error("❌ Indexing test failed for {$engine} with {$size} documents: " . $e->getMessage());
                    $results["{$engine}_{$size}"] = [
                        'dataset_size' => $size,
                        'error' => $e->getMessage(),
                        'success' => false
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * 🇻🇳 Test Vietnamese language handling
     */
    public function runVietnameseLanguageTests(array $options = []): array
    {
        $testQueries = $this->testDataGenerator->generateVietnameseTestQueries();
        $results = [];

        foreach (['elasticsearch', 'solr'] as $engine) {
            $engineResults = [];
            
            foreach ($testQueries as $query => $description) {
                $startTime = microtime(true);
                $result = $this->unifiedSearch->searchBoth($query);
                $endTime = microtime(true);
                
                $responseTime = ($endTime - $startTime) * 1000;
                $resultCount = $result[$engine]['count'];
                
                // Save benchmark result
                $benchmarkResult = BenchmarkResult::create([
                    'engine' => $engine,
                    'test_type' => 'vietnamese_language',
                    'test_name' => "Vietnamese: {$description}",
                    'dataset_size' => 0,
                    'iterations' => 1,
                    'response_time_avg' => $responseTime,
                    'successful_queries' => 1,
                    'failed_queries' => 0,
                    'success_rate' => 100.0,
                    'results_count' => $resultCount,
                    'test_parameters' => json_encode([
                        'query' => $query,
                        'description' => $description,
                        'language' => 'vietnamese'
                    ]),
                    'test_started_at' => now(),
                    'test_completed_at' => now()
                ]);
                
                $engineResults[$query] = [
                    'description' => $description,
                    'response_time' => $responseTime,
                    'results_count' => $resultCount,
                    'benchmark_id' => $benchmarkResult->id
                ];
            }
            
            $results[$engine] = $engineResults;
        }

        return $results;
    }

    /**
     * ⚡ Test load performance
     */
    public function runLoadTests(array $options = []): array
    {
        $loadScenarios = $this->testDataGenerator->generateLoadTestScenarios();
        $results = [];

        foreach ($loadScenarios as $scenarioName => $scenario) {
            $results[$scenarioName] = $this->runLoadTestScenario($scenario);
        }

        return $results;
    }

    /**
     * 🖥️ Test resource usage
     */
    public function runResourceUsageTests(array $options = []): array
    {
        $results = [];
        
        // This would integrate with Docker stats or system monitoring
        // For now, we'll return placeholder structure
        $results['elasticsearch'] = [
            'memory_usage' => 'N/A - requires Docker stats integration',
            'cpu_usage' => 'N/A - requires Docker stats integration',
            'disk_usage' => 'N/A - requires Docker stats integration'
        ];
        
        $results['solr'] = [
            'memory_usage' => 'N/A - requires Docker stats integration',
            'cpu_usage' => 'N/A - requires Docker stats integration',
            'disk_usage' => 'N/A - requires Docker stats integration'
        ];

        return $results;
    }

    /**
     * 📊 Generate benchmark summary
     */
    public function generateBenchmarkSummary(array $results): array
    {
        $summary = [
            'total_tests' => 0,
            'elasticsearch_wins' => 0,
            'solr_wins' => 0,
            'ties' => 0,
            'performance_winner' => null,
            'resource_winner' => null,
            'overall_winner' => null
        ];

        // Analyze search performance
        if (isset($results['search_performance'])) {
            $esAvgTime = $this->calculateAverageResponseTime($results['search_performance']['elasticsearch']);
            $solrAvgTime = $this->calculateAverageResponseTime($results['search_performance']['solr']);
            
            if ($esAvgTime < $solrAvgTime) {
                $summary['elasticsearch_wins']++;
                $summary['performance_winner'] = 'elasticsearch';
            } elseif ($solrAvgTime < $esAvgTime) {
                $summary['solr_wins']++;
                $summary['performance_winner'] = 'solr';
            } else {
                $summary['ties']++;
            }
            
            $summary['total_tests']++;
        }

        // Analyze indexing performance
        if (isset($results['indexing_performance'])) {
            $esIndexing = $this->analyzeIndexingPerformance($results['indexing_performance'], 'elasticsearch');
            $solrIndexing = $this->analyzeIndexingPerformance($results['indexing_performance'], 'solr');
            
            if ($esIndexing['avg_throughput'] > $solrIndexing['avg_throughput']) {
                $summary['elasticsearch_wins']++;
            } elseif ($solrIndexing['avg_throughput'] > $esIndexing['avg_throughput']) {
                $summary['solr_wins']++;
            } else {
                $summary['ties']++;
            }
            
            $summary['total_tests']++;
        }

        // Determine overall winner
        if ($summary['elasticsearch_wins'] > $summary['solr_wins']) {
            $summary['overall_winner'] = 'elasticsearch';
        } elseif ($summary['solr_wins'] > $summary['elasticsearch_wins']) {
            $summary['overall_winner'] = 'solr';
        } else {
            $summary['overall_winner'] = 'tie';
        }

        return $summary;
    }

    /**
     * 📈 Calculate statistics from array of times
     */
    private function calculateStatistics(array $times): array
    {
        sort($times);
        $count = count($times);
        
        return [
            'avg' => round(array_sum($times) / $count, 2),
            'min' => round(min($times), 2),
            'max' => round(max($times), 2),
            'p50' => round($times[intval($count * 0.5)], 2),
            'p95' => round($times[intval($count * 0.95)], 2),
            'p99' => round($times[intval($count * 0.99)], 2),
            'throughput' => round($count / (array_sum($times) / 1000), 2)
        ];
    }

    /**
     * 📊 Calculate average response time from engine results
     */
    private function calculateAverageResponseTime(array $engineResults): float
    {
        $times = [];
        foreach ($engineResults as $result) {
            if (isset($result['statistics']['avg'])) {
                $times[] = $result['statistics']['avg'];
            }
        }
        
        return empty($times) ? 0 : array_sum($times) / count($times);
    }

    /**
     * 📝 Analyze indexing performance
     */
    private function analyzeIndexingPerformance(array $results, string $engine): array
    {
        $throughputs = [];
        foreach ($results as $key => $result) {
            if (strpos($key, $engine) === 0 && isset($result['throughput'])) {
                $throughputs[] = $result['throughput'];
            }
        }
        
        return [
            'avg_throughput' => empty($throughputs) ? 0 : array_sum($throughputs) / count($throughputs),
            'max_throughput' => empty($throughputs) ? 0 : max($throughputs),
            'min_throughput' => empty($throughputs) ? 0 : min($throughputs)
        ];
    }

    /**
     * 🏃‍♂️ Run single load test scenario
     */
    private function runLoadTestScenario(array $scenario): array
    {
        // This would implement actual load testing
        // For now, return placeholder structure
        return [
            'scenario' => $scenario,
            'elasticsearch' => [
                'avg_response_time' => 'N/A - requires load testing implementation',
                'throughput' => 'N/A - requires load testing implementation',
                'error_rate' => 'N/A - requires load testing implementation'
            ],
            'solr' => [
                'avg_response_time' => 'N/A - requires load testing implementation',
                'throughput' => 'N/A - requires load testing implementation',
                'error_rate' => 'N/A - requires load testing implementation'
            ]
        ];
    }

    /**
     * 📝 Index documents to Elasticsearch
     */
    private function indexToElasticsearch(array $listings): bool
    {
        try {
            foreach ($listings as $listing) {
                $this->unifiedSearch->elasticsearch->indexDocument('listings', $listing['id'], $listing);
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('❌ Elasticsearch indexing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📝 Index documents to Solr
     */
    private function indexToSolr(array $listings): bool
    {
        try {
            return $this->unifiedSearch->solr->bulkIndex($listings);
        } catch (\Throwable $e) {
            Log::error('❌ Solr indexing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📊 Get benchmark results from database
     */
    public function getBenchmarkResults(array $filters = []): array
    {
        $query = BenchmarkResult::query();
        
        if (isset($filters['engine'])) {
            $query->where('engine', $filters['engine']);
        }
        
        if (isset($filters['test_type'])) {
            $query->where('test_type', $filters['test_type']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        
        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * 🧹 Clear old benchmark results
     */
    public function clearOldResults(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        return BenchmarkResult::where('created_at', '<', $cutoffDate)->delete();
    }
}
