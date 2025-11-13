<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SupportContextService
{
    private array $stopWords = [
        'anh','chị','chi','em','toi','tôi','ban','bạn','mình','minh','cần','giúp','giup','help','hỗ','hotro','hỗtrợ','support',
        'cho','xin','chào','hello','hi','này','nay','nha','với','ve','nữa','nua','có','không','ko',
        'là','và','hay','hoặc','được','duoc','gì','gi','các','những','một','muốn','nhờ','nhé','nhỉ','oi','ơi','thanks','cảm','ơn'
    ];

    /**
     * @return array{context:?string,products:array<int,array<string,mixed>>}
     */
    public function buildContext(string $userMessage): array
    {
        $keywords = $this->extractKeywords($userMessage);

        $listings = $this->searchListings($keywords);
        $categories = $this->searchCategories($keywords);

        $sections = [];
        if ($listings->isNotEmpty()) {
            $sections[] = "Sản phẩm phù hợp trong kho:\n" . $listings->map(function (Listing $listing) {
                $price = number_format((float) $listing->price, 0, ',', '.');
                $category = $listing->category->name ?? 'Khác';
                $condition = Str::of($listing->condition ?? 'N/A')->replace('_', ' ')->upper();
                return sprintf('- #%d %s (%s) • %s đ • %s', $listing->id, $listing->title, $category, $price, $condition);
            })->implode("\n");
        }

        if ($categories->isNotEmpty()) {
            $sections[] = "Danh mục gợi ý nổi bật:\n" . $categories->map(function (Category $category) {
                $count = $category->listings_count ?? 0;
                return sprintf('- %s • %d tin đang bán', $category->name, $count);
            })->implode("\n");
        }

        return [
            'context' => $sections ? implode("\n\n", $sections) : null,
            'products' => $this->formatProductCards($listings),
        ];
    }

    private function extractKeywords(string $message): array
    {
        $tokens = preg_split('/[^\p{L}\p{N}]+/u', Str::lower($message), -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            return [];
        }

        return collect($tokens)
            ->reject(fn ($token) => mb_strlen($token) <= 2 || in_array($token, $this->stopWords, true))
            ->unique()
            ->values()
            ->take(6)
            ->all();
    }

    private function searchListings(array $keywords): Collection
    {
        $query = Listing::query()
            ->with([
                'category',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            ])
            ->where('status', 'approved')
            ->orderByDesc('approved_at')
            ->limit(5);

        if ($keywords) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('title', 'like', "%{$word}%")
                      ->orWhere('description', 'like', "%{$word}%");
                }
            });
        }

        return $query->get();
    }

    private function searchCategories(array $keywords): Collection
    {
        $query = Category::query()
            ->withCount(['listings' => fn ($q) => $q->where('status', 'approved')])
            ->orderByDesc('listings_count')
            ->limit(5);

        if ($keywords) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('name', 'like', "%{$word}%")
                      ->orWhere('description', 'like', "%{$word}%");
                }
            });
        }

        return $query->get();
    }

    private function formatProductCards(Collection $listings): array
    {
        if ($listings->isEmpty()) {
            return [];
        }

        return $listings->map(function (Listing $listing) {
            $image = $listing->images->sortByDesc('is_primary')->first();
            $thumbnail = $image && $image->image_path ? URL::to('storage/' . ltrim($image->image_path, '/')) : null;

            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'price' => (float) $listing->price,
                'thumbnail' => $thumbnail,
                'category' => $listing->category->name ?? null,
                'url' => '/listings/' . $listing->id,
            ];
        })->all();
    }
}
