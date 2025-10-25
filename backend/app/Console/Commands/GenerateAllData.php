<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateAllData extends Command
{
    protected $signature = 'generate:all-data {--count=1500}';
    protected $description = 'Generate all random data for the marketplace';

    public function handle()
    {
        $count = $this->option('count');
        
        $this->info('🚀 Starting comprehensive data generation...');
        $this->newLine();
        
        // Step 1: Generate users
        $this->info('👥 Step 1: Generating users...');
        Artisan::call('generate:random-data', ['--users' => 200]);
        $this->info('✅ Users generated');
        $this->newLine();
        
        // Step 2: Generate categories
        $this->info('📂 Step 2: Generating categories...');
        Artisan::call('generate:random-data', ['--categories' => 30]);
        $this->info('✅ Categories generated');
        $this->newLine();
        
        // Step 3: Generate listings
        $this->info('📝 Step 3: Generating listings...');
        Artisan::call('generate:sample-data', ['count' => $count]);
        $this->info('✅ Listings generated');
        $this->newLine();
        
        // Step 4: Generate relationships
        $this->info('🔗 Step 4: Generating relationships...');
        Artisan::call('generate:random-data', [
            '--wishlists' => $count * 0.3,
            '--offers' => $count * 0.2,
            '--reviews' => $count * 0.15
        ]);
        $this->info('✅ Relationships generated');
        $this->newLine();
        
        $this->info('🎉 All data generation completed successfully!');
        $this->info("📊 Generated approximately {$count} listings with related data");
    }
}
