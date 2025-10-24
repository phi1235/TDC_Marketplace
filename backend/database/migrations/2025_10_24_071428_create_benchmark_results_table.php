<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('benchmark_results', function (Blueprint $table) {
            $table->id();
            $table->string('engine'); // 'elasticsearch' or 'solr'
            $table->string('test_type'); // 'search_simple', 'search_complex', 'indexing', 'concurrent', 'vietnamese'
            $table->string('test_name'); // Descriptive name of the test
            $table->integer('dataset_size')->default(0); // Number of documents in dataset
            $table->integer('iterations')->default(1); // Number of test iterations
            
            // Performance metrics
            $table->float('response_time_avg', 8, 2)->nullable(); // Average response time in ms
            $table->float('response_time_min', 8, 2)->nullable(); // Minimum response time in ms
            $table->float('response_time_max', 8, 2)->nullable(); // Maximum response time in ms
            $table->float('response_time_p50', 8, 2)->nullable(); // 50th percentile
            $table->float('response_time_p95', 8, 2)->nullable(); // 95th percentile
            $table->float('response_time_p99', 8, 2)->nullable(); // 99th percentile
            
            // Throughput metrics
            $table->float('throughput', 8, 2)->nullable(); // Queries per second
            $table->integer('successful_queries')->default(0);
            $table->integer('failed_queries')->default(0);
            $table->float('success_rate', 5, 2)->nullable(); // Success rate percentage
            
            // Resource usage
            $table->float('memory_usage_mb', 8, 2)->nullable(); // Memory usage in MB
            $table->float('cpu_usage_percent', 5, 2)->nullable(); // CPU usage percentage
            $table->float('disk_usage_mb', 8, 2)->nullable(); // Disk usage in MB
            
            // Search quality metrics
            $table->integer('results_count')->default(0); // Number of results returned
            $table->float('relevance_score', 5, 2)->nullable(); // Average relevance score
            $table->float('precision', 5, 2)->nullable(); // Precision score
            $table->float('recall', 5, 2)->nullable(); // Recall score
            
            // Test configuration
            $table->json('test_parameters')->nullable(); // Test configuration as JSON
            $table->json('query_details')->nullable(); // Query details as JSON
            $table->text('notes')->nullable(); // Additional notes
            
            // Metadata
            $table->string('test_environment')->default('development'); // Test environment
            $table->string('tested_by')->nullable(); // Who ran the test
            $table->timestamp('test_started_at')->nullable();
            $table->timestamp('test_completed_at')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['engine', 'test_type']);
            $table->index(['test_type', 'dataset_size']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benchmark_results');
    }
};