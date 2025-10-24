<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenchmarkResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'engine',
        'test_type',
        'test_name',
        'dataset_size',
        'iterations',
        'response_time_avg',
        'response_time_min',
        'response_time_max',
        'response_time_p50',
        'response_time_p95',
        'response_time_p99',
        'throughput',
        'successful_queries',
        'failed_queries',
        'success_rate',
        'memory_usage_mb',
        'cpu_usage_percent',
        'disk_usage_mb',
        'results_count',
        'relevance_score',
        'precision',
        'recall',
        'test_parameters',
        'query_details',
        'notes',
        'test_environment',
        'tested_by',
        'test_started_at',
        'test_completed_at',
    ];

    protected $casts = [
        'test_parameters' => 'array',
        'query_details' => 'array',
        'test_started_at' => 'datetime',
        'test_completed_at' => 'datetime',
    ];

    public function scopeByEngine($query, $engine)
    {
        return $query->where('engine', $engine);
    }

    public function scopeByTestName($query, $testName)
    {
        return $query->where('test_name', $testName);
    }

    public function scopeByMetric($query, $metric)
    {
        return $query->where('metric', $metric);
    }
}
