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

        $listings = Listing::with('images')->get();
        $count = 0;

        foreach ($listings as $listing) {
            // ✅ Lấy ảnh đầu tiên từ quan hệ images
            $firstImage = $listing->images()->first();
            $imageUrl = null;

            if ($firstImage && $firstImage->image_path) {
                // vì cột là image_path, chứa đường dẫn tương đối
                $imageUrl = 'http://localhost:8001/storage/' . $firstImage->image_path;
            }

            $success = $es->indexDocument('listings', $listing->id, [
                'title' => $listing->title,
                'title_suggest' => [
                    'input' => explode(' ', $listing->title),
                    'weight' => 1,
                ],
                'description' => $listing->description,
                'price' => (float) $listing->price,
                'category_id' => (int) $listing->category_id,
                'image' => $imageUrl, // 👈 thêm field ảnh
                'status' => $listing->status,
                'created_at' => optional($listing->created_at)->toISOString(),
            ]);

            if ($success) $count++;
        }


        $this->info("✅ Done indexing $count listings into Elasticsearch!");
    }
}


