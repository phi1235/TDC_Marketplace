<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InitSolrCore extends Command
{
    protected $signature = 'solr:init {--force : Force recreation of core}';
    protected $description = 'Initialize Solr core for listings with Vietnamese language support';

    public function handle()
    {
        $this->info('🚀 Initializing Solr core for listings...');

        // Check if Solr is running
        if (!$this->checkSolrHealth()) {
            $this->error('❌ Solr is not running or not accessible');
            return Command::FAILURE;
        }

        $this->info('✅ Solr is running');

        // Check if core already exists
        if ($this->coreExists() && !$this->option('force')) {
            $this->warn('⚠️  Core "listings" already exists. Use --force to recreate.');
            return Command::SUCCESS;
        }

        // Delete existing core if force option is used
        if ($this->coreExists() && $this->option('force')) {
            $this->info('🗑️  Deleting existing core...');
            $this->deleteCore();
        }

        // Create the core
        if ($this->createCore()) {
            $this->info('✅ Core "listings" created successfully');
            
            // Add sample data
            if ($this->addSampleData()) {
                $this->info('✅ Sample data added successfully');
            }
            
            $this->info('🎉 Solr initialization completed!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Failed to create Solr core');
            return Command::FAILURE;
        }
    }

    protected function checkSolrHealth(): bool
    {
        try {
            $response = Http::timeout(10)->get(config('solr.url') . '/admin/ping');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Solr health check failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function coreExists(): bool
    {
        try {
            $response = Http::timeout(10)->get(config('solr.url') . '/admin/cores');
            $data = $response->json();
            return isset($data['status']['listings']);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function deleteCore(): bool
    {
        try {
            $response = Http::timeout(30)->post(config('solr.url') . '/admin/cores', [
                'action' => 'UNLOAD',
                'core' => 'listings'
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to delete Solr core: ' . $e->getMessage());
            return false;
        }
    }

    protected function createCore(): bool
    {
        try {
            $response = Http::timeout(30)->post(config('solr.url') . '/admin/cores', [
                'action' => 'CREATE',
                'name' => 'listings',
                'configSet' => 'listings'
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to create Solr core: ' . $e->getMessage());
            return false;
        }
    }

    protected function addSampleData(): bool
    {
        try {
            $sampleData = [
                [
                    'id' => '1',
                    'title' => 'Sách giáo khoa Toán lớp 10',
                    'description' => 'Sách giáo khoa Toán lớp 10, tình trạng tốt, ít sử dụng',
                    'price' => 50000,
                    'original_price' => 80000,
                    'category_id' => 1,
                    'seller_id' => 1,
                    'condition_grade' => 'A',
                    'status' => 'active',
                    'created_at' => now()->toISOString()
                ],
                [
                    'id' => '2',
                    'title' => 'Điện thoại iPhone 12',
                    'description' => 'Điện thoại iPhone 12, màu xanh, tình trạng tốt',
                    'price' => 15000000,
                    'original_price' => 20000000,
                    'category_id' => 2,
                    'seller_id' => 2,
                    'condition_grade' => 'B',
                    'status' => 'active',
                    'created_at' => now()->toISOString()
                ],
                [
                    'id' => '3',
                    'title' => 'Máy tính xách tay Dell',
                    'description' => 'Máy tính xách tay Dell, RAM 8GB, SSD 256GB',
                    'price' => 12000000,
                    'original_price' => 18000000,
                    'category_id' => 3,
                    'seller_id' => 3,
                    'condition_grade' => 'A',
                    'status' => 'active',
                    'created_at' => now()->toISOString()
                ]
            ];

            $response = Http::timeout(30)->post(
                config('solr.url') . '/listings/update/json/docs?commit=true',
                $sampleData
            );

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to add sample data: ' . $e->getMessage());
            return false;
        }
    }
}
