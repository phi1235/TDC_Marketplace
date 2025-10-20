<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Services\ElasticSearchService;

class IndexListingsToES extends Command
{
    protected $signature = 'es:index-listings';
    protected $description = 'Index all listings into Elasticsearch';

    public function handle(ElasticSearchService $es)
    {
        $this->info('🚀 Indexing listings into Elasticsearch...');

        $listings = Listing::all();
        $count = 0;

        foreach ($listings as $listing) {
            $success = $es->indexDocument('listings', $listing->id, [
                'title' => $listing->title,
                'title_suggest' => [
                    'input' => explode(' ', $listing->title), // tách từ khóa ra để suggest tốt hơn
                    'weight' => 1
                ],
                'description' => $listing->description,
                'price' => (float) $listing->price,
                'category_id' => (int) $listing->category_id,
            ]);

            if ($success) $count++;
        }

        $this->info("✅ Done indexing $count listings into Elasticsearch!");
    }
}
