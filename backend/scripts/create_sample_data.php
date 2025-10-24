<?php

/**
 * Script tạo dữ liệu mẫu cho Comparison Dashboard
 * Chạy: php scripts/create_sample_data.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\BenchmarkResult;
use App\Models\ResourceMetric;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Creating sample data for Comparison Dashboard...\n";

// Tạo dữ liệu benchmark results
echo "📊 Creating benchmark results...\n";

$benchmarkData = [
    [
        'engine' => 'elasticsearch',
        'test_type' => 'performance',
        'keyword' => 'iPhone 15',
        'response_time' => 25.5,
        'results_count' => 12,
        'success' => true,
        'error_message' => null,
        'tested_at' => now()->subHours(1),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'test_type' => 'performance',
        'keyword' => 'iPhone 15',
        'response_time' => 23.2,
        'results_count' => 15,
        'success' => true,
        'error_message' => null,
        'tested_at' => now()->subHours(1),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'elasticsearch',
        'test_type' => 'performance',
        'keyword' => 'MacBook Pro',
        'response_time' => 28.1,
        'results_count' => 8,
        'success' => true,
        'error_message' => null,
        'tested_at' => now()->subHours(2),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'test_type' => 'performance',
        'keyword' => 'MacBook Pro',
        'response_time' => 26.8,
        'results_count' => 10,
        'success' => true,
        'error_message' => null,
        'tested_at' => now()->subHours(2),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'elasticsearch',
        'test_type' => 'vietnamese',
        'keyword' => 'sách giáo khoa',
        'response_time' => 31.2,
        'results_count' => 5,
        'success' => true,
        'error_message' => null,
        'tested_at' => now()->subHours(3),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'test_type' => 'vietnamese',
        'keyword' => 'sách giáo khoa',
        'response_time' => 29.5,
        'results_count' => 7,
        'success' => true,
        'error_message' => null,
        'tested_at' => now()->subHours(3),
        'created_at' => now(),
        'updated_at' => now()
    ]
];

foreach ($benchmarkData as $data) {
    BenchmarkResult::create($data);
}

echo "✅ Created " . count($benchmarkData) . " benchmark results\n";

// Tạo dữ liệu resource metrics
echo "🖥️ Creating resource metrics...\n";

$resourceData = [
    [
        'engine' => 'elasticsearch',
        'container_name' => 'tdc-elasticsearch',
        'metric_type' => 'memory',
        'value' => 512.5,
        'unit' => 'MB',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'elasticsearch',
        'container_name' => 'tdc-elasticsearch',
        'metric_type' => 'cpu',
        'value' => 15.2,
        'unit' => '%',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'elasticsearch',
        'container_name' => 'tdc-elasticsearch',
        'metric_type' => 'disk',
        'value' => 1024.8,
        'unit' => 'MB',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'elasticsearch',
        'container_name' => 'tdc-elasticsearch',
        'metric_type' => 'network',
        'value' => 256.3,
        'unit' => 'KB/s',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'container_name' => 'tdc-solr',
        'metric_type' => 'memory',
        'value' => 384.2,
        'unit' => 'MB',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'container_name' => 'tdc-solr',
        'metric_type' => 'cpu',
        'value' => 12.8,
        'unit' => '%',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'container_name' => 'tdc-solr',
        'metric_type' => 'disk',
        'value' => 768.5,
        'unit' => 'MB',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'engine' => 'solr',
        'container_name' => 'tdc-solr',
        'metric_type' => 'network',
        'value' => 198.7,
        'unit' => 'KB/s',
        'status' => 'active',
        'measured_at' => now()->subMinutes(30),
        'created_at' => now(),
        'updated_at' => now()
    ]
];

foreach ($resourceData as $data) {
    ResourceMetric::create($data);
}

echo "✅ Created " . count($resourceData) . " resource metrics\n";

echo "🎉 Sample data created successfully!\n";
echo "Now refresh your dashboard to see the data.\n";
