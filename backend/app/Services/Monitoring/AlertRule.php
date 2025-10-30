<?php

namespace App\Services\Monitoring;

interface AlertRule
{
    public function evaluate(array $context): ?array; // return alert array or null
}


