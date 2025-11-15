<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Services\SolrService;

class IndexListingsToSolr extends Command
{
    protected $signature = 'solr:index-listings';
    protected $description = 'Index all listings into Solr';

    public function handle(SolrService $solr)
    {
        $listings = Listing::with('images')->get();
        $count = 0;

        foreach ($listings as $listing) {
            $firstImage = $listing->images->first();
            $imageUrl = $firstImage ? 'http://localhost:8001/storage/' . $firstImage->image_path : null;

            $success = $solr->indexDocument([
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'price' => (float) $listing->price,
                'category_id' => (int) $listing->category_id,
                'image' => $imageUrl,
                'status' => $listing->status,
                'created_at' => optional($listing->created_at)->toAtomString(),
            ]);

            if ($success) $count++;
        }

        $this->info("✅ Indexed $count listings to Solr successfully!");
    }
}
