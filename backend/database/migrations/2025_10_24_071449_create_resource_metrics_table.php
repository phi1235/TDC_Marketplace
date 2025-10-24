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
        Schema::create('resource_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('engine'); // 'elasticsearch' or 'solr'
            $table->string('container_name'); // Docker container name
            $table->string('metric_type'); // 'memory', 'cpu', 'disk', 'network'
            
            // Resource usage values
            $table->float('value', 10, 4); // The metric value
            $table->string('unit'); // Unit of measurement (MB, %, GB, etc.)
            $table->string('status')->default('active'); // active, idle, under_load
            
            // Context information
            $table->integer('concurrent_users')->default(0); // Number of concurrent users
            $table->integer('active_queries')->default(0); // Number of active queries
            $table->string('test_scenario')->nullable(); // Test scenario being run
            
            // Timestamps
            $table->timestamp('measured_at');
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['engine', 'metric_type']);
            $table->index(['engine', 'measured_at']);
            $table->index('measured_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_metrics');
    }
};