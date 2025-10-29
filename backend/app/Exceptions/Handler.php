<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Services\MonitoringService;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            try {
                $monitoringService = app(MonitoringService::class);
                $status = ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface)
                    ? $e->getStatusCode() : 500;
                $monitoringService->logError([
                    'level' => 'error',
                    'status' => $status,
                    'message' => $e->getMessage(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null,
                    'route' => request()->path(),
                    'method' => request()->method(),
                    'user_id' => optional(request()->user())->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'request_id' => request()->header('X-Request-Id'),
                ]);
            } catch (\Throwable $ignore) {}
        });
    }
    public function render($request, Throwable $exception)
{
    if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    return parent::render($request, $exception);
}
}
