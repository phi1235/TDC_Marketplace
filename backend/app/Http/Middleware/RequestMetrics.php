<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\MonitoringService;
use Illuminate\Support\Facades\Auth;

class RequestMetrics
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        /** @var Response $response */
        $response = $next($request);
        try {
            $monitoringService = app(MonitoringService::class);
            $monitoringService->logMetric([
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status' => $response->getStatusCode(),
                'duration_ms' => (int) round((microtime(true) - $start) * 1000),
                'user_id' => optional(Auth::user())->id,
            ]);
        } catch (\Throwable $e) {
            // swallow
        }
        return $response;
    }
}


