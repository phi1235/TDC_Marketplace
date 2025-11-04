<?php

namespace App\Services\Monitoring;

class P95LatencyRule implements AlertRule
{
    public function __construct(private int $thresholdMs = 1500) {}

    public function evaluate(array $context): ?array
    {
        $p95 = (int)($context['p95_ms'] ?? 0);
        if ($p95 > $this->thresholdMs) {
            return [
                'rule' => 'p95',
                'level' => 'warning',
                'message' => 'p95 response > '.$this->thresholdMs.'ms',
                'context' => [ 'p95_ms' => $p95 ],
            ];
        }
        return null;
    }
}


