<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mount broadcasting auth under /api with Sanctum token auth
        Broadcast::routes([
            'middleware' => ['auth:sanctum', 'api'],
            'prefix' => 'api',
        ]);

        require base_path('routes/channels.php');
    }
}
