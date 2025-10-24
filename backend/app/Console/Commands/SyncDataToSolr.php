<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Services\SolrService;
use Illuminate\Console\Command;

class SyncDataToSolr extends Command
{
    protected $signature = 'solr:sync {--clear : Clear existing data first}';
    protected $description = 'Sync database listings to Solr';

    protected SolrService $solrService;

    public function __construct(SolrService $solrService)
    {
        parent::__construct();
        $this->solrService = $solrService;
    }

    public function handle()
    {
        $this->info('🔄 Syncing database listings to Solr...');

        // Check Solr health
        if (!$this->solrService->ping()) {
            $this->error('❌ Solr is not accessible');
            return Command::FAILURE;
        }

        $this->info('✅ Solr is accessible');

        // Clear existing data if requested
        if ($this->option('clear')) {
            $this->info('🗑️  Clearing existing Solr data...');
            $this->solrService->clearIndex();
        }

        // Get all listings from database
        $listings = Listing::with(['seller', 'category', 'images'])->get();
        $this->info("📊 Found {$listings->count()} listings in database");

        $successCount = 0;
        $errorCount = 0;

        foreach ($listings as $listing) {
            try {
                // Prepare document for Solr
                $document = [
                    'id' => (string) $listing->id,
                    'title' => $listing->title,
                    'description' => $listing->description,
                    'price' => (float) $listing->price,
                    'category_id' => $listing->category_id,
                    'condition_grade' => $listing->condition,
                    'status' => $listing->status,
                    'seller_id' => $listing->seller_id,
                    'created_at' => $listing->created_at->toISOString(),
                    'updated_at' => $listing->updated_at->toISOString(),
                ];

                // Add to Solr
                $this->solrService->indexDocument((string) $listing->id, $document);
                $successCount++;

                if ($successCount % 10 === 0) {
                    $this->info("📝 Processed {$successCount} listings...");
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to index listing {$listing->id}: " . $e->getMessage());
                $errorCount++;
            }
        }

        // Commit changes
        $this->solrService->commit();

        $this->info("✅ Sync completed!");
        $this->info("📊 Success: {$successCount}, Errors: {$errorCount}");

        // Verify by counting documents
        $count = $this->solrService->getDocumentCount();
        $this->info("📈 Solr now contains {$count} documents");

        return Command::SUCCESS;
    }
}