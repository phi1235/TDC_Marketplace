<?php

namespace App\Http\Controllers;

use App\Services\UnifiedSearchService;
use App\Services\ElasticSearchService;
use App\Services\SolrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LoadTestController extends Controller
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
     * 🚀 Run load test
     */
    public function runLoadTest(Request $request)
    {
        $request->validate([
            'concurrent_users' => 'required|integer|min:1|max:100',
            'requests_per_user' => 'required|integer|min:1|max:50',
            'test_duration' => 'required|integer|min:10|max:300', // seconds
            'keywords' => 'required|array|min:1',
            'engine' => 'required|in:elasticsearch,solr,both'
        ]);

        $concurrentUsers = $request->concurrent_users;
        $requestsPerUser = $request->requests_per_user;
        $testDuration = $request->test_duration;
        $keywords = $request->keywords;
        $engine = $request->engine;

        $results = $this->executeLoadTest($concurrentUsers, $requestsPerUser, $testDuration, $keywords, $engine);

        return response()->json([
            'success' => true,
            'test_config' => [
                'concurrent_users' => $concurrentUsers,
                'requests_per_user' => $requestsPerUser,
                'test_duration' => $testDuration,
                'total_requests' => $concurrentUsers * $requestsPerUser
            ],
            'results' => $results,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * 📊 Get load test results
     */
    public function getLoadTestResults(Request $request)
    {
        $results = $this->getStoredLoadTestResults($request->get('limit', 10));

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * 🔄 Execute load test
     */
    private function executeLoadTest(int $concurrentUsers, int $requestsPerUser, int $testDuration, array $keywords, string $engine): array
    {
        $startTime = microtime(true);
        $results = [
            'elasticsearch' => [
                'total_requests' => 0,
                'successful_requests' => 0,
                'failed_requests' => 0,
                'total_response_time' => 0,
                'min_response_time' => PHP_FLOAT_MAX,
                'max_response_time' => 0,
                'errors' => []
            ],
            'solr' => [
                'total_requests' => 0,
                'successful_requests' => 0,
                'failed_requests' => 0,
                'total_response_time' => 0,
                'min_response_time' => PHP_FLOAT_MAX,
                'max_response_time' => 0,
                'errors' => []
            ]
        ];

        $processes = [];
        $endTime = $startTime + $testDuration;

        // Simulate concurrent users
        for ($i = 0; $i < $concurrentUsers; $i++) {
            $processes[] = $this->simulateUser($requestsPerUser, $keywords, $engine, $endTime);
        }

        // Wait for all processes to complete
        foreach ($processes as $process) {
            $processResults = $process();
            $this->mergeResults($results, $processResults);
        }

        $totalTime = microtime(true) - $startTime;

        // Calculate final metrics
        $this->calculateFinalMetrics($results, $totalTime);

        // Store results
        $this->storeLoadTestResults($results, $concurrentUsers, $requestsPerUser, $testDuration);

        return $results;
    }

    /**
     * 👤 Simulate a single user
     */
    private function simulateUser(int $requestsPerUser, array $keywords, string $engine, float $endTime): callable
    {
        return function() use ($requestsPerUser, $keywords, $engine, $endTime) {
            $userResults = [
                'elasticsearch' => [
                    'total_requests' => 0,
                    'successful_requests' => 0,
                    'failed_requests' => 0,
                    'total_response_time' => 0,
                    'min_response_time' => PHP_FLOAT_MAX,
                    'max_response_time' => 0,
                    'errors' => []
                ],
                'solr' => [
                    'total_requests' => 0,
                    'successful_requests' => 0,
                    'failed_requests' => 0,
                    'total_response_time' => 0,
                    'min_response_time' => PHP_FLOAT_MAX,
                    'max_response_time' => 0,
                    'errors' => []
                ]
            ];

            for ($i = 0; $i < $requestsPerUser && microtime(true) < $endTime; $i++) {
                $keyword = $keywords[array_rand($keywords)];
                
                if ($engine === 'elasticsearch' || $engine === 'both') {
                    $this->executeSearchRequest('elasticsearch', $keyword, $userResults['elasticsearch']);
                }
                
                if ($engine === 'solr' || $engine === 'both') {
                    $this->executeSearchRequest('solr', $keyword, $userResults['solr']);
                }

                // Small delay between requests
                usleep(10000); // 10ms
            }

            return $userResults;
        };
    }

    /**
     * 🔍 Execute search request
     */
    private function executeSearchRequest(string $engine, string $keyword, array &$results): void
    {
        $startTime = microtime(true);
        $results['total_requests']++;

        try {
            if ($engine === 'elasticsearch') {
                $response = $this->elasticSearch->search('listings', $keyword);
                $success = !empty($response['hits']['hits']);
            } else {
                $response = $this->solr->search($keyword);
                $success = !empty($response['response']['docs']);
            }

            $responseTime = (microtime(true) - $startTime) * 1000; // Convert to ms

            if ($success) {
                $results['successful_requests']++;
                $results['total_response_time'] += $responseTime;
                $results['min_response_time'] = min($results['min_response_time'], $responseTime);
                $results['max_response_time'] = max($results['max_response_time'], $responseTime);
            } else {
                $results['failed_requests']++;
                $results['errors'][] = "No results for keyword: {$keyword}";
            }

        } catch (\Throwable $e) {
            $results['failed_requests']++;
            $results['errors'][] = $e->getMessage();
        }
    }

    /**
     * 🔄 Merge results from different processes
     */
    private function mergeResults(array &$results, array $processResults): void
    {
        foreach (['elasticsearch', 'solr'] as $engine) {
            $results[$engine]['total_requests'] += $processResults[$engine]['total_requests'];
            $results[$engine]['successful_requests'] += $processResults[$engine]['successful_requests'];
            $results[$engine]['failed_requests'] += $processResults[$engine]['failed_requests'];
            $results[$engine]['total_response_time'] += $processResults[$engine]['total_response_time'];
            $results[$engine]['min_response_time'] = min($results[$engine]['min_response_time'], $processResults[$engine]['min_response_time']);
            $results[$engine]['max_response_time'] = max($results[$engine]['max_response_time'], $processResults[$engine]['max_response_time']);
            $results[$engine]['errors'] = array_merge($results[$engine]['errors'], $processResults[$engine]['errors']);
        }
    }

    /**
     * 📊 Calculate final metrics
     */
    private function calculateFinalMetrics(array &$results, float $totalTime): void
    {
        foreach (['elasticsearch', 'solr'] as $engine) {
            if ($results[$engine]['total_requests'] > 0) {
                $results[$engine]['avg_response_time'] = $results[$engine]['total_response_time'] / $results[$engine]['total_requests'];
                $results[$engine]['success_rate'] = ($results[$engine]['successful_requests'] / $results[$engine]['total_requests']) * 100;
                $results[$engine]['requests_per_second'] = $results[$engine]['total_requests'] / $totalTime;
            } else {
                $results[$engine]['avg_response_time'] = 0;
                $results[$engine]['success_rate'] = 0;
                $results[$engine]['requests_per_second'] = 0;
            }

            // Reset min if no successful requests
            if ($results[$engine]['min_response_time'] === PHP_FLOAT_MAX) {
                $results[$engine]['min_response_time'] = 0;
            }
        }
    }

    /**
     * 💾 Store load test results
     */
    private function storeLoadTestResults(array $results, int $concurrentUsers, int $requestsPerUser, int $testDuration): void
    {
        // Store in database or cache
        $testResult = [
            'concurrent_users' => $concurrentUsers,
            'requests_per_user' => $requestsPerUser,
            'test_duration' => $testDuration,
            'results' => $results,
            'created_at' => now()
        ];

        // Store in cache for now (in production, store in database)
        cache()->put('load_test_results_' . now()->timestamp, $testResult, 3600);
    }

    /**
     * 📋 Get stored load test results
     */
    private function getStoredLoadTestResults(int $limit): array
    {
        // In production, fetch from database
        return [
            'message' => 'Load test results storage not implemented yet',
            'limit' => $limit
        ];
    }
}
