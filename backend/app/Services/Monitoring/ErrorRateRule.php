<?php

namespace App\Services\Monitoring;

class ErrorRateRule implements AlertRule
{
    public function __construct(private float $threshold = 0.02) {}

    public function evaluate(array $context): ?array
    {
        $total = (int)($context['total_requests'] ?? 0);
        $errors = (int)($context['error_requests'] ?? 0);
        $rate = $total > 0 ? $errors / $total : 0;
        if ($rate > $this->threshold) {
            return [
                'rule' => 'error_rate',
                'level' => 'warning',
                'message' => 'Error rate > '.($this->threshold*100).'% trong khoảng thời gian',
                'context' => [ 'rate' => round($rate*100, 2) ],
            ];
        }
        return null;
    }
}


