<?php

namespace App\Http\Controllers;

use App\Services\SearchBenchmarkService;
use App\Services\PerformanceMonitor;
use App\Services\SearchQualityAnalyzer;
use App\Services\UnifiedSearchService;
use App\Models\BenchmarkResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComparisonController extends Controller
{
    protected $benchmarkService;
    protected $performanceMonitor;
    protected $qualityAnalyzer;
    protected $unifiedSearch;

    public function __construct(
        SearchBenchmarkService $benchmarkService,
        PerformanceMonitor $performanceMonitor,
        SearchQualityAnalyzer $qualityAnalyzer,
        UnifiedSearchService $unifiedSearch
    ) {
        $this->benchmarkService = $benchmarkService;
        $this->performanceMonitor = $performanceMonitor;
        $this->qualityAnalyzer = $qualityAnalyzer;
        $this->unifiedSearch = $unifiedSearch;
    }

    /**
     * 📊 Get performance comparison metrics
     */
    public function performance(Request $request)
    {
        try {
            $filters = $request->only(['engine', 'test_type', 'date_from', 'date_to']);
            $results = $this->benchmarkService->getBenchmarkResults($filters);

            $performanceData = $this->analyzePerformanceData($results);

            return response()->json([
                'success' => true,
                'data' => $performanceData,
                'filters_applied' => $filters
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Performance comparison failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get performance comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🖥️ Get resource usage comparison
     */
    public function resources(Request $request)
    {
        try {
            $hours = $request->get('hours', 24);
            $comparison = $this->performanceMonitor->getResourceComparison($hours);

            return response()->json([
                'success' => true,
                'data' => $comparison,
                'time_range_hours' => $hours
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Resource comparison failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get resource comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🎯 Get search quality comparison
     */
    public function quality(Request $request)
    {
        try {
            $testQueries = $request->get('queries', []);
            $qualityResults = $this->qualityAnalyzer->analyzeSearchQuality($testQueries);

            return response()->json([
                'success' => true,
                'data' => $qualityResults
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Quality comparison failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get quality comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📈 Get overall comparison summary
     */
    public function summary(Request $request)
    {
        try {
            $summary = [
                'performance' => $this->getPerformanceSummary(),
                'resources' => $this->getResourceSummary(),
                'quality' => $this->getQualitySummary(),
                'health' => $this->getHealthSummary(),
                'overall_winner' => null
            ];

            // Determine overall winner
            $summary['overall_winner'] = $this->determineOverallWinner($summary);

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Summary comparison failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get comparison summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧪 Run live comparison test
     */
    public function runTest(Request $request)
    {
        try {
            $testType = $request->get('test_type', 'performance');
            $options = $request->get('options', []);

            $results = [];

            switch ($testType) {
                case 'performance':
                    $results = $this->benchmarkService->runSearchPerformanceTests($options);
                    break;
                case 'indexing':
                    $results = $this->benchmarkService->runIndexingPerformanceTests($options);
                    break;
                case 'vietnamese':
                    $results = $this->benchmarkService->runVietnameseLanguageTests($options);
                    break;
                case 'quality':
                    $testQueries = $request->get('queries', []);
                    $results = $this->qualityAnalyzer->analyzeSearchQuality($testQueries);
                    break;
                case 'full':
                    $results = $this->benchmarkService->runFullBenchmark($options);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid test type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'test_type' => $testType,
                'data' => $results,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Live test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Test execution failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Get real-time metrics
     */
    public function realtime()
    {
        try {
            $metrics = [
                'health' => $this->unifiedSearch->healthCheck(),
                'resources' => $this->performanceMonitor->getRealTimeMetrics(),
                'timestamp' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Real-time metrics failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get real-time metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📈 Get performance trends
     */
    public function trends(Request $request)
    {
        try {
            $days = $request->get('days', 7);
            $engine = $request->get('engine', 'both');

            $trends = [];

            if ($engine === 'both' || $engine === 'elasticsearch') {
                $trends['elasticsearch'] = $this->getEngineTrends('elasticsearch', $days);
            }

            if ($engine === 'both' || $engine === 'solr') {
                $trends['solr'] = $this->getEngineTrends('solr', $days);
            }

            return response()->json([
                'success' => true,
                'data' => $trends,
                'time_range_days' => $days
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ Trends analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get trends',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Analyze performance data
     */
    private function analyzePerformanceData(array $results): array
    {
        $analysis = [
            'elasticsearch' => [
                'avg_response_time' => 0,
                'total_tests' => 0,
                'success_rate' => 0,
                'throughput' => 0
            ],
            'solr' => [
                'avg_response_time' => 0,
                'total_tests' => 0,
                'success_rate' => 0,
                'throughput' => 0
            ]
        ];

        foreach ($results as $result) {
            $engine = $result['engine'];
            
            if (isset($analysis[$engine])) {
                $analysis[$engine]['total_tests']++;
                $analysis[$engine]['avg_response_time'] += $result['response_time_avg'] ?? 0;
                $analysis[$engine]['success_rate'] += $result['success_rate'] ?? 0;
                $analysis[$engine]['throughput'] += $result['throughput'] ?? 0;
            }
        }

        // Calculate averages
        foreach ($analysis as $engine => $data) {
            if ($data['total_tests'] > 0) {
                $analysis[$engine]['avg_response_time'] = round($data['avg_response_time'] / $data['total_tests'], 2);
                $analysis[$engine]['success_rate'] = round($data['success_rate'] / $data['total_tests'], 2);
                $analysis[$engine]['throughput'] = round($data['throughput'] / $data['total_tests'], 2);
            }
        }

        return $analysis;
    }

    /**
     * 📊 Get performance summary
     */
    private function getPerformanceSummary(): array
    {
        $recentResults = $this->benchmarkService->getBenchmarkResults([
            'date_from' => now()->subDays(7)->toDateString()
        ]);

        $esResults = collect($recentResults)->where('engine', 'elasticsearch');
        $solrResults = collect($recentResults)->where('engine', 'solr');

        return [
            'elasticsearch' => [
                'avg_response_time' => $esResults->avg('response_time_avg') ?? 0,
                'total_tests' => $esResults->count(),
                'success_rate' => $esResults->avg('success_rate') ?? 0
            ],
            'solr' => [
                'avg_response_time' => $solrResults->avg('response_time_avg') ?? 0,
                'total_tests' => $solrResults->count(),
                'success_rate' => $solrResults->avg('success_rate') ?? 0
            ],
            'winner' => $this->determinePerformanceWinner($esResults, $solrResults)
        ];
    }

    /**
     * 🖥️ Get resource summary
     */
    private function getResourceSummary(): array
    {
        $esStats = $this->performanceMonitor->calculateResourceStatistics('elasticsearch', 24);
        $solrStats = $this->performanceMonitor->calculateResourceStatistics('solr', 24);

        return [
            'elasticsearch' => $esStats,
            'solr' => $solrStats,
            'winner' => $this->determineResourceWinner($esStats, $solrStats)
        ];
    }

    /**
     * 🎯 Get quality summary
     */
    private function getQualitySummary(): array
    {
        $defaultQueries = [
            'sách giáo khoa' => ['sách', 'giáo khoa'],
            'laptop cũ' => ['laptop', 'máy tính'],
            'máy tính bảng' => ['tablet', 'máy tính']
        ];

        $qualityResults = $this->qualityAnalyzer->analyzeSearchQuality($defaultQueries);

        return [
            'elasticsearch' => $this->calculateQualityAverages($qualityResults['elasticsearch'] ?? []),
            'solr' => $this->calculateQualityAverages($qualityResults['solr'] ?? []),
            'winner' => $qualityResults['comparison']['overall_winner'] ?? 'tie'
        ];
    }

    /**
     * 🏥 Get health summary
     */
    private function getHealthSummary(): array
    {
        $health = $this->unifiedSearch->healthCheck();

        return [
            'elasticsearch' => $health['elasticsearch']['status'],
            'solr' => $health['solr']['status'],
            'overall' => $health['overall_status']
        ];
    }

    /**
     * 🏆 Determine overall winner
     */
    private function determineOverallWinner(array $summary): string
    {
        $esWins = 0;
        $solrWins = 0;

        // Performance winner
        if ($summary['performance']['winner'] === 'elasticsearch') {
            $esWins++;
        } elseif ($summary['performance']['winner'] === 'solr') {
            $solrWins++;
        }

        // Resource winner
        if ($summary['resources']['winner'] === 'elasticsearch') {
            $esWins++;
        } elseif ($summary['resources']['winner'] === 'solr') {
            $solrWins++;
        }

        // Quality winner
        if ($summary['quality']['winner'] === 'elasticsearch') {
            $esWins++;
        } elseif ($summary['quality']['winner'] === 'solr') {
            $solrWins++;
        }

        if ($esWins > $solrWins) {
            return 'elasticsearch';
        } elseif ($solrWins > $esWins) {
            return 'solr';
        } else {
            return 'tie';
        }
    }

    /**
     * 📈 Get engine trends
     */
    private function getEngineTrends(string $engine, int $days): array
    {
        $trends = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $results = $this->benchmarkService->getBenchmarkResults([
                'engine' => $engine,
                'date_from' => $date,
                'date_to' => $date
            ]);

            $trends[] = [
                'date' => $date,
                'avg_response_time' => collect($results)->avg('response_time_avg') ?? 0,
                'total_tests' => count($results),
                'success_rate' => collect($results)->avg('success_rate') ?? 0
            ];
        }

        return $trends;
    }

    /**
     * 🏆 Determine performance winner
     */
    private function determinePerformanceWinner($esResults, $solrResults): string
    {
        $esAvgTime = $esResults->avg('response_time_avg') ?? 0;
        $solrAvgTime = $solrResults->avg('response_time_avg') ?? 0;

        if ($esAvgTime < $solrAvgTime) {
            return 'elasticsearch';
        } elseif ($solrAvgTime < $esAvgTime) {
            return 'solr';
        } else {
            return 'tie';
        }
    }

    /**
     * 🏆 Determine resource winner
     */
    private function determineResourceWinner(array $esStats, array $solrStats): string
    {
        $esMemory = $esStats['memory']['avg'] ?? 0;
        $solrMemory = $solrStats['memory']['avg'] ?? 0;

        if ($esMemory < $solrMemory) {
            return 'elasticsearch';
        } elseif ($solrMemory < $esMemory) {
            return 'solr';
        } else {
            return 'tie';
        }
    }

    /**
     * 📊 Calculate quality averages
     */
    private function calculateQualityAverages(array $results): array
    {
        if (empty($results)) {
            return [
                'precision' => 0,
                'recall' => 0,
                'f1_score' => 0,
                'vietnamese_score' => 0,
                'relevance_score' => 0,
                'fuzzy_score' => 0,
                'overall_score' => 0
            ];
        }

        $averages = [
            'precision' => 0,
            'recall' => 0,
            'f1_score' => 0,
            'vietnamese_score' => 0,
            'relevance_score' => 0,
            'fuzzy_score' => 0,
            'overall_score' => 0
        ];

        $count = count($results);
        foreach ($results as $result) {
            $metrics = $result['quality_metrics'] ?? [];
            foreach ($averages as $key => $value) {
                $averages[$key] += $metrics[$key] ?? 0;
            }
        }

        foreach ($averages as $key => $value) {
            $averages[$key] = round($value / $count, 3);
        }

        return $averages;
    }

    /**
     * 📊 Get recent benchmark results for dashboard
     */
    public function getRecentResults(Request $request)
    {
        try {
            $limit = $request->get('limit', 20);
            $results = BenchmarkResult::latest()
                ->limit($limit)
                ->get()
                ->map(function ($result) {
                    return [
                        'id' => $result->id,
                        'engine' => $result->engine,
                        'test_name' => $result->test_name,
                        'response_time_avg' => round($result->response_time_avg, 2),
                        'throughput' => round($result->throughput, 2),
                        'success_rate' => round($result->success_rate, 1),
                        'created_at' => $result->created_at->toISOString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to get recent results: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent results'
            ], 500);
        }
    }
}
