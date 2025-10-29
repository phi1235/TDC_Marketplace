<?php

namespace App\Services;

use App\Models\SystemError;
use App\Models\SystemMetric;
use App\Models\SystemAlert;
use App\Services\Monitoring\AlertRule;
use App\Services\Monitoring\ErrorRateRule;
use App\Services\Monitoring\P95LatencyRule;
use Illuminate\Support\Facades\DB;

class MonitoringService
{
    /** @var AlertRule[] */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new ErrorRateRule(0.02),
            new P95LatencyRule(1500),
        ];
    }
    public function logError(array $data): void
    {
        try {
            SystemError::create([
                'level' => $data['level'] ?? 'error',
                'status' => $data['status'] ?? null,
                'message' => substr($data['message'] ?? '', 0, 512),
                'trace' => isset($data['trace']) ? substr($data['trace'], 0, 4000) : null,
                'route' => $data['route'] ?? null,
                'method' => $data['method'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
                'request_id' => $data['request_id'] ?? null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail to avoid recursion
        }
    }

    public function logMetric(array $data): void
    {
        try {
            SystemMetric::create([
                'endpoint' => $data['endpoint'] ?? '',
                'method' => $data['method'] ?? '',
                'status' => $data['status'] ?? 200,
                'duration_ms' => $data['duration_ms'] ?? 0,
                'user_id' => $data['user_id'] ?? null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail to avoid recursion
        }
    }

    public function getOverview(int $hours = 24, ?string $endpoint = null, ?int $status = null): array
    {
        $since = now()->subHours($hours);

        $errorsQuery = DB::table('system_errors')
            ->orderByDesc('created_at')
            ->limit(50);
        if ($endpoint) { $errorsQuery->where('route', 'like', "%$endpoint%"); }
        if ($status) { $errorsQuery->where('status', $status); }
        $errors = $errorsQuery->get();

        $errorRateQuery = DB::table('system_metrics')
            ->select('status', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $since)
            ->groupBy('status');
        if ($endpoint) { $errorRateQuery->where('endpoint', 'like', "%$endpoint%"); }
        if ($status) { $errorRateQuery->where('status', $status); }
        $errorRate = $errorRateQuery->pluck('total', 'status');

        $p95Query = DB::table('system_metrics')
            ->where('created_at', '>=', $since)
            ->orderBy('duration_ms');
        if ($endpoint) { $p95Query->where('endpoint', 'like', "%$endpoint%"); }
        if ($status) { $p95Query->where('status', $status); }
        $p95 = $p95Query->pluck('duration_ms');

        // Safe p95 for small samples
        if ($p95->isEmpty()) {
            $p95v = 0;
        } else {
            $n = $p95->count();
            $idx = max(0, min($n - 1, (int) ceil(0.95 * $n) - 1));
            $p95v = (int) $p95[$idx];
        }

        $total = array_sum($errorRate->toArray());
        $errorsCount = array_sum(array_map(
            fn($s, $c) => ((int) $s >= 500) ? $c : 0,
            array_keys($errorRate->toArray()),
            $errorRate->toArray()
        ));

        // Per-hour series (last N hours)
        $seriesQuery = DB::table('system_metrics')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00') as h"),
                     DB::raw('COUNT(*) as total'),
                     DB::raw('SUM(CASE WHEN status >= 500 THEN 1 ELSE 0 END) as errors'),
                     DB::raw('AVG(duration_ms) as avg_ms'))
            ->where('created_at', '>=', $since)
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00')"))
            ->orderBy('h');
        if ($endpoint) { $seriesQuery->where('endpoint', 'like', "%$endpoint%"); }
        if ($status) { $seriesQuery->where('status', $status); }
        $series = $seriesQuery->get();

        // Evaluate simple alert rules (on-demand)
        $alerts = $this->evaluateAlerts($since, $errorRate->toArray(), $p95v);

        return [
            'error_rate' => [
                'total' => $total,
                'errors' => $errorsCount,
            ],
            'p95_response_ms' => $p95v,
            'recent_errors' => $errors,
            'series' => $series,
            'alerts' => $alerts,
        ];
    }

    public function exportCsv(int $hours = 24, ?string $endpoint = null, ?int $status = null): string
    {
        $since = now()->subHours($hours);
        $q = DB::table('system_metrics')->where('created_at','>=',$since);
        if ($endpoint) { $q->where('endpoint','like',"%$endpoint%"); }
        if ($status) { $q->where('status',$status); }
        $rows = $q->orderByDesc('created_at')->limit(5000)->get(['created_at','endpoint','method','status','duration_ms','user_id']);

        $csv = implode(",", ['created_at','endpoint','method','status','duration_ms','user_id'])."\n";
        foreach ($rows as $r) {
            $csv .= implode(",", [
                (string)$r->created_at,
                '"'.str_replace('"','""',$r->endpoint).'"',
                $r->method,
                $r->status,
                $r->duration_ms,
                $r->user_id ?? ''
            ])."\n";
        }
        return $csv;
    }

    protected function evaluateAlerts($since, array $errorRateMap, int $p95ms): array
    {
        $total = array_sum($errorRateMap);
        $errors = array_sum(array_map(fn($s,$c)=> ((int)$s>=500)?$c:0, array_keys($errorRateMap), $errorRateMap));
        $rate = $total > 0 ? ($errors / $total) : 0;

        $alerts = [];
        $context = [ 'total_requests' => $total, 'error_requests' => $errors, 'p95_ms' => $p95ms ];
        foreach ($this->rules as $rule) {
            $a = $rule->evaluate($context);
            if ($a) {
                $alerts[] = $this->createAlertOnce($a['rule'], $a['level'], $a['message'], $a['context'] ?? []);
            }
        }
        return $alerts;
    }

    protected function createAlertOnce(string $rule, string $level, string $message, array $context): array
    {
        $existing = SystemAlert::where('rule',$rule)->where('active',true)->orderByDesc('created_at')->first();
        if (!$existing) {
            SystemAlert::create([
                'rule' => $rule,
                'level' => $level,
                'message' => $message,
                'context' => $context,
                'active' => true,
                'created_at' => now(),
            ]);
        }
        return [ 'rule' => $rule, 'level' => $level, 'message' => $message, 'context' => $context ];
    }
}

