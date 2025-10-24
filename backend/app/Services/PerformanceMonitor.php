<?php

namespace App\Services;

use App\Models\ResourceMetric;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    protected $dockerHost;

    public function __construct()
    {
        $this->dockerHost = env('DOCKER_HOST', 'unix:///var/run/docker.sock');
    }

    /**
     * 📊 Collect resource metrics for a container
     */
    public function collectResourceMetrics(string $containerName, string $engine): array
    {
        try {
            $stats = $this->getDockerStats($containerName);
            
            if (!$stats) {
                return $this->getPlaceholderMetrics($engine);
            }

            $metrics = [
                'memory' => $this->parseMemoryUsage($stats['memory_stats']),
                'cpu' => $this->parseCpuUsage($stats['cpu_stats']),
                'disk' => $this->parseDiskUsage($stats['blkio_stats']),
                'network' => $this->parseNetworkUsage($stats['networks'])
            ];

            // Store metrics in database
            $this->storeResourceMetrics($engine, $containerName, $metrics);

            return $metrics;

        } catch (\Throwable $e) {
            Log::error("❌ Failed to collect metrics for {$containerName}: " . $e->getMessage());
            return $this->getPlaceholderMetrics($engine);
        }
    }

    /**
     * 🐳 Get Docker container stats
     */
    private function getDockerStats(string $containerName): ?array
    {
        try {
            // This would typically use Docker API
            // For now, return null to use placeholder metrics
            return null;
        } catch (\Throwable $e) {
            Log::error("❌ Docker stats failed for {$containerName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 📝 Store resource metrics in database
     */
    private function storeResourceMetrics(string $engine, string $containerName, array $metrics): void
    {
        foreach ($metrics as $type => $value) {
            ResourceMetric::create([
                'engine' => $engine,
                'container_name' => $containerName,
                'metric_type' => $type,
                'value' => $value['value'],
                'unit' => $value['unit'],
                'status' => 'active',
                'measured_at' => now()
            ]);
        }
    }

    /**
     * 🧮 Parse memory usage from Docker stats
     */
    private function parseMemoryUsage(array $memoryStats): array
    {
        if (!isset($memoryStats['usage'])) {
            return ['value' => 0, 'unit' => 'MB'];
        }

        $usage = $memoryStats['usage'];
        $limit = $memoryStats['limit'] ?? $usage;
        
        return [
            'value' => round($usage / 1024 / 1024, 2), // Convert to MB
            'unit' => 'MB',
            'percentage' => $limit > 0 ? round(($usage / $limit) * 100, 2) : 0
        ];
    }

    /**
     * 🖥️ Parse CPU usage from Docker stats
     */
    private function parseCpuUsage(array $cpuStats): array
    {
        if (!isset($cpuStats['cpu_usage']['total_usage'])) {
            return ['value' => 0, 'unit' => '%'];
        }

        $totalUsage = $cpuStats['cpu_usage']['total_usage'];
        $systemUsage = $cpuStats['system_cpu_usage'] ?? $totalUsage;
        
        $cpuPercent = 0;
        if ($systemUsage > 0) {
            $cpuPercent = ($totalUsage / $systemUsage) * 100;
        }

        return [
            'value' => round($cpuPercent, 2),
            'unit' => '%'
        ];
    }

    /**
     * 💾 Parse disk usage from Docker stats
     */
    private function parseDiskUsage(array $blkioStats): array
    {
        if (!isset($blkioStats['io_service_bytes'])) {
            return ['value' => 0, 'unit' => 'MB'];
        }

        $totalBytes = 0;
        foreach ($blkioStats['io_service_bytes'] as $device) {
            if (isset($device['Read']) && isset($device['Write'])) {
                $totalBytes += $device['Read'] + $device['Write'];
            }
        }

        return [
            'value' => round($totalBytes / 1024 / 1024, 2), // Convert to MB
            'unit' => 'MB'
        ];
    }

    /**
     * 🌐 Parse network usage from Docker stats
     */
    private function parseNetworkUsage(array $networks): array
    {
        $totalBytes = 0;
        foreach ($networks as $network) {
            if (isset($network['rx_bytes']) && isset($network['tx_bytes'])) {
                $totalBytes += $network['rx_bytes'] + $network['tx_bytes'];
            }
        }

        return [
            'value' => round($totalBytes / 1024 / 1024, 2), // Convert to MB
            'unit' => 'MB'
        ];
    }

    /**
     * 📊 Get placeholder metrics when Docker stats unavailable
     */
    private function getPlaceholderMetrics(string $engine): array
    {
        return [
            'memory' => [
                'value' => 0,
                'unit' => 'MB',
                'percentage' => 0,
                'note' => 'Docker stats integration required'
            ],
            'cpu' => [
                'value' => 0,
                'unit' => '%',
                'note' => 'Docker stats integration required'
            ],
            'disk' => [
                'value' => 0,
                'unit' => 'MB',
                'note' => 'Docker stats integration required'
            ],
            'network' => [
                'value' => 0,
                'unit' => 'MB',
                'note' => 'Docker stats integration required'
            ]
        ];
    }

    /**
     * 📈 Get resource metrics history
     */
    public function getResourceMetricsHistory(string $engine, int $hours = 24): array
    {
        $startTime = now()->subHours($hours);
        
        $metrics = ResourceMetric::where('engine', $engine)
            ->where('measured_at', '>=', $startTime)
            ->orderBy('measured_at', 'asc')
            ->get()
            ->groupBy('metric_type');

        $history = [];
        foreach ($metrics as $type => $records) {
            $history[$type] = $records->map(function ($record) {
                return [
                    'value' => $record->value,
                    'unit' => $record->unit,
                    'measured_at' => $record->measured_at,
                    'status' => $record->status
                ];
            })->toArray();
        }

        return $history;
    }

    /**
     * 📊 Calculate resource usage statistics
     */
    public function calculateResourceStatistics(string $engine, int $hours = 24): array
    {
        $startTime = now()->subHours($hours);
        
        $metrics = ResourceMetric::where('engine', $engine)
            ->where('measured_at', '>=', $startTime)
            ->get()
            ->groupBy('metric_type');

        $statistics = [];
        foreach ($metrics as $type => $records) {
            $values = $records->pluck('value')->toArray();
            
            if (!empty($values)) {
                $statistics[$type] = [
                    'avg' => round(array_sum($values) / count($values), 2),
                    'min' => round(min($values), 2),
                    'max' => round(max($values), 2),
                    'current' => round(end($values), 2),
                    'unit' => $records->first()->unit ?? 'unknown',
                    'sample_count' => count($values)
                ];
            }
        }

        return $statistics;
    }

    /**
     * 🚨 Check for resource alerts
     */
    public function checkResourceAlerts(string $engine): array
    {
        $alerts = [];
        $statistics = $this->calculateResourceStatistics($engine, 1); // Last hour

        // Memory alert
        if (isset($statistics['memory']) && $statistics['memory']['current'] > 1000) {
            $alerts[] = [
                'type' => 'memory',
                'level' => 'warning',
                'message' => "High memory usage: {$statistics['memory']['current']}MB",
                'engine' => $engine
            ];
        }

        // CPU alert
        if (isset($statistics['cpu']) && $statistics['cpu']['current'] > 80) {
            $alerts[] = [
                'type' => 'cpu',
                'level' => 'warning',
                'message' => "High CPU usage: {$statistics['cpu']['current']}%",
                'engine' => $engine
            ];
        }

        return $alerts;
    }

    /**
     * 📊 Get real-time resource metrics
     */
    public function getRealTimeMetrics(): array
    {
        $elasticsearchMetrics = $this->collectResourceMetrics('tdc-elasticsearch', 'elasticsearch');
        $solrMetrics = $this->collectResourceMetrics('tdc-solr', 'solr');

        return [
            'elasticsearch' => $elasticsearchMetrics,
            'solr' => $solrMetrics,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * 🧹 Clean up old resource metrics
     */
    public function cleanupOldMetrics(int $daysOld = 7): int
    {
        $cutoffDate = now()->subDays($daysOld);
        return ResourceMetric::where('measured_at', '<', $cutoffDate)->delete();
    }

    /**
     * 📈 Get resource comparison between engines
     */
    public function getResourceComparison(int $hours = 24): array
    {
        $esStats = $this->calculateResourceStatistics('elasticsearch', $hours);
        $solrStats = $this->calculateResourceStatistics('solr', $hours);

        $comparison = [];
        foreach (['memory', 'cpu', 'disk', 'network'] as $metric) {
            if (isset($esStats[$metric]) && isset($solrStats[$metric])) {
                $comparison[$metric] = [
                    'elasticsearch' => $esStats[$metric],
                    'solr' => $solrStats[$metric],
                    'winner' => $this->determineResourceWinner($esStats[$metric], $solrStats[$metric], $metric)
                ];
            }
        }

        return $comparison;
    }

    /**
     * 🏆 Determine resource usage winner
     */
    private function determineResourceWinner(array $esStats, array $solrStats, string $metric): string
    {
        // For memory, disk, network: lower is better
        // For CPU: depends on context, but generally lower is better
        $esValue = $esStats['avg'];
        $solrValue = $solrStats['avg'];

        if ($esValue < $solrValue) {
            return 'elasticsearch';
        } elseif ($solrValue < $esValue) {
            return 'solr';
        } else {
            return 'tie';
        }
    }
}
