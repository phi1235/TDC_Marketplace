<?php
// Test API endpoint để kiểm tra ComparisonController
// Chạy: php test-api.php

// Mock data để test
$mockData = [
    'success' => true,
    'data' => [
        'performance' => [
            'elasticsearch' => [
                'avg_response_time' => 8.5,
                'throughput' => 120.5,
                'success_rate' => 99.2
            ],
            'solr' => [
                'avg_response_time' => 6.2,
                'throughput' => 145.8,
                'success_rate' => 99.5
            ]
        ],
        'quality' => [
            'elasticsearch' => [
                'precision' => 0.85,
                'recall' => 0.78,
                'f1_score' => 0.81,
                'vietnamese_score' => 0.88
            ],
            'solr' => [
                'precision' => 0.89,
                'recall' => 0.82,
                'f1_score' => 0.85,
                'vietnamese_score' => 0.91
            ]
        ],
        'overall_winner' => 'solr'
    ]
];

echo "=== TEST API RESPONSE ===\n";
echo json_encode($mockData, JSON_PRETTY_PRINT);
echo "\n\n=== FRONTEND ACCESS ===\n";
echo "1. Admin Dashboard: http://localhost:5173/admin\n";
echo "2. Comparison Dashboard: http://localhost:5173/admin/comparison\n";
echo "3. Search Test: http://localhost:5173/search-test\n";
echo "\n=== BACKEND API ENDPOINTS ===\n";
echo "1. Summary: http://localhost:8001/api/comparison/summary\n";
echo "2. Performance: http://localhost:8001/api/comparison/performance\n";
echo "3. Quality: http://localhost:8001/api/comparison/quality\n";
echo "4. Recent: http://localhost:8001/api/comparison/recent\n";
?>
