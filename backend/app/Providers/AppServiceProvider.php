<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Dispute;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce short morph names to avoid class-string issues in polymorphic relations
        Relation::enforceMorphMap([
            'listing' => \App\Models\Listing::class,
            'user' => \App\Models\User::class,
            'review' => \App\Models\Review::class,
            'report' => \App\Models\Report::class,
                    'dispute' => Dispute::class,

        ]);
    }
}
