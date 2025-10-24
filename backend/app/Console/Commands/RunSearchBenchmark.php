<?php

namespace App\Console\Commands;

use App\Services\SearchBenchmarkService;
use App\Services\TestDataGenerator;
use Illuminate\Console\Command;

class RunSearchBenchmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:search 
                            {--type=all : Type of benchmark to run (all, performance, indexing, vietnamese, load)}
                            {--iterations=10 : Number of iterations for performance tests}
                            {--dataset-sizes=100,1000,10000 : Comma-separated dataset sizes for indexing tests}
                            {--output=console : Output format (console, json, csv)}
                            {--save : Save results to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run comprehensive search engine benchmarks comparing Elasticsearch and Solr';

    protected $benchmarkService;
    protected $testDataGenerator;

    public function __construct(SearchBenchmarkService $benchmarkService, TestDataGenerator $testDataGenerator)
    {
        parent::__construct();
        $this->benchmarkService = $benchmarkService;
        $this->testDataGenerator = $testDataGenerator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Search Engine Benchmark...');
        $this->newLine();

        $type = $this->option('type');
        $iterations = (int) $this->option('iterations');
        $datasetSizes = array_map('intval', explode(',', $this->option('dataset-sizes')));
        $output = $this->option('output');
        $save = $this->option('save');

        $options = [
            'iterations' => $iterations,
            'dataset_sizes' => $datasetSizes,
            'save' => $save
        ];

        try {
            switch ($type) {
                case 'performance':
                    $this->runPerformanceBenchmark($options);
                    break;
                case 'indexing':
                    $this->runIndexingBenchmark($options);
                    break;
                case 'vietnamese':
                    $this->runVietnameseBenchmark($options);
                    break;
                case 'load':
                    $this->runLoadBenchmark($options);
                    break;
                case 'all':
                default:
                    $this->runFullBenchmark($options);
                    break;
            }

            $this->newLine();
            $this->info('✅ Benchmark completed successfully!');

        } catch (\Throwable $e) {
            $this->error('❌ Benchmark failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * 🏃‍♂️ Run full benchmark suite
     */
    private function runFullBenchmark(array $options)
    {
        $this->info('📊 Running Full Benchmark Suite...');
        
        $results = $this->benchmarkService->runFullBenchmark($options);
        
        $this->displayResults($results);
    }

    /**
     * 🔍 Run performance benchmark
     */
    private function runPerformanceBenchmark(array $options)
    {
        $this->info('🔍 Running Search Performance Tests...');
        
        $progressBar = $this->output->createProgressBar(100);
        $progressBar->start();

        $results = $this->benchmarkService->runSearchPerformanceTests($options);
        
        $progressBar->finish();
        $this->newLine();
        
        $this->displayPerformanceResults($results);
    }

    /**
     * 📝 Run indexing benchmark
     */
    private function runIndexingBenchmark(array $options)
    {
        $this->info('📝 Running Indexing Performance Tests...');
        
        $datasetSizes = $options['dataset_sizes'];
        $totalTests = count($datasetSizes) * 2; // 2 engines
        $progressBar = $this->output->createProgressBar($totalTests);
        $progressBar->start();

        $results = $this->benchmarkService->runIndexingPerformanceTests($options);
        
        $progressBar->finish();
        $this->newLine();
        
        $this->displayIndexingResults($results);
    }

    /**
     * 🇻🇳 Run Vietnamese language benchmark
     */
    private function runVietnameseBenchmark(array $options)
    {
        $this->info('🇻🇳 Running Vietnamese Language Tests...');
        
        $testQueries = $this->testDataGenerator->generateVietnameseTestQueries();
        $totalTests = count($testQueries) * 2; // 2 engines
        $progressBar = $this->output->createProgressBar($totalTests);
        $progressBar->start();

        $results = $this->benchmarkService->runVietnameseLanguageTests($options);
        
        $progressBar->finish();
        $this->newLine();
        
        $this->displayVietnameseResults($results);
    }

    /**
     * ⚡ Run load benchmark
     */
    private function runLoadBenchmark(array $options)
    {
        $this->info('⚡ Running Load Tests...');
        
        $results = $this->benchmarkService->runLoadTests($options);
        
        $this->displayLoadResults($results);
    }

    /**
     * 📊 Display performance results
     */
    private function displayPerformanceResults(array $results)
    {
        $this->info('📊 Search Performance Results:');
        $this->newLine();

        foreach (['elasticsearch', 'solr'] as $engine) {
            $this->info("🔍 {$engine}:");
            
            $engineResults = $results[$engine] ?? [];
            $totalTime = 0;
            $totalTests = 0;

            foreach ($engineResults as $query => $result) {
                $avgTime = $result['statistics']['avg'] ?? 0;
                $totalTime += $avgTime;
                $totalTests++;
                
                $this->line("  • {$query}: {$avgTime}ms avg");
            }

            if ($totalTests > 0) {
                $overallAvg = $totalTime / $totalTests;
                $this->info("  📈 Overall Average: {$overallAvg}ms");
            }
            
            $this->newLine();
        }
    }

    /**
     * 📝 Display indexing results
     */
    private function displayIndexingResults(array $results)
    {
        $this->info('📝 Indexing Performance Results:');
        $this->newLine();

        foreach (['elasticsearch', 'solr'] as $engine) {
            $this->info("📝 {$engine}:");
            
            foreach ($results as $key => $result) {
                if (strpos($key, $engine) === 0) {
                    $datasetSize = $result['dataset_size'] ?? 0;
                    $totalTime = $result['total_time'] ?? 0;
                    $throughput = $result['throughput'] ?? 0;
                    $success = $result['success'] ?? false;
                    
                    $status = $success ? '✅' : '❌';
                    $this->line("  {$status} {$datasetSize} docs: {$totalTime}ms total, {$throughput} docs/sec");
                }
            }
            
            $this->newLine();
        }
    }

    /**
     * 🇻🇳 Display Vietnamese language results
     */
    private function displayVietnameseResults(array $results)
    {
        $this->info('🇻🇳 Vietnamese Language Test Results:');
        $this->newLine();

        foreach (['elasticsearch', 'solr'] as $engine) {
            $this->info("🇻🇳 {$engine}:");
            
            $engineResults = $results[$engine] ?? [];
            foreach ($engineResults as $query => $result) {
                $responseTime = $result['response_time'] ?? 0;
                $resultsCount = $result['results_count'] ?? 0;
                $description = $result['description'] ?? '';
                
                $this->line("  • {$query} ({$description}): {$responseTime}ms, {$resultsCount} results");
            }
            
            $this->newLine();
        }
    }

    /**
     * ⚡ Display load test results
     */
    private function displayLoadResults(array $results)
    {
        $this->info('⚡ Load Test Results:');
        $this->newLine();

        foreach ($results as $scenarioName => $scenario) {
            $this->info("⚡ {$scenarioName}:");
            
            foreach (['elasticsearch', 'solr'] as $engine) {
                $engineResults = $scenario[$engine] ?? [];
                $avgTime = $engineResults['avg_response_time'] ?? 'N/A';
                $throughput = $engineResults['throughput'] ?? 'N/A';
                $errorRate = $engineResults['error_rate'] ?? 'N/A';
                
                $this->line("  • {$engine}: {$avgTime}ms avg, {$throughput} qps, {$errorRate}% errors");
            }
            
            $this->newLine();
        }
    }

    /**
     * 📊 Display all results
     */
    private function displayResults(array $results)
    {
        $this->info('📊 Benchmark Results Summary:');
        $this->newLine();

        // Display summary
        if (isset($results['summary'])) {
            $summary = $results['summary'];
            $this->info("🏆 Overall Winner: {$summary['overall_winner']}");
            $this->info("📈 Performance Winner: {$summary['performance_winner']}");
            $this->info("💾 Resource Winner: {$summary['resource_winner']}");
            $this->newLine();
        }

        // Display individual test results
        if (isset($results['search_performance'])) {
            $this->displayPerformanceResults($results['search_performance']);
        }

        if (isset($results['indexing_performance'])) {
            $this->displayIndexingResults($results['indexing_performance']);
        }

        if (isset($results['vietnamese_tests'])) {
            $this->displayVietnameseResults($results['vietnamese_tests']);
        }

        if (isset($results['load_tests'])) {
            $this->displayLoadResults($results['load_tests']);
        }
    }
}