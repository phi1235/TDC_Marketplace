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
        $this->info('Indexing listings into Elasticsearch...');
        $listings = Listing::all();

        foreach ($listings as $listing) {
            $es->indexDocument('listings', $listing->id, [
                'name' => $listing->title,
                'description' => $listing->description,
                'price' => $listing->price,
                'category' => $listing->category_id,
            ]);
        }

        $this->info('✅ Done indexing listings!');
    }
}
