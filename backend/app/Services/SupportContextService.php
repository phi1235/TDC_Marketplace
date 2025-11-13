<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Support\Str;

class SupportContextService
{
    private array $stopWords = [
        'anh','chị','em','toi','tôi','ban','mình','cần','giúp','help','hỗ','trợ','hotro','support',
        'cho','xin','chào','hello','hi','này','nha','với','về','thế','nào','nữa','có','không','ko',
        'là','và','hay','hoặc','được','gì','gối','các','những','một','muốn','nhờ','nhé','nhỉ','ơi',
    ];

    public function buildContext(string $userMessage): ?string
    {
        $keywords = $this->extractKeywords($userMessage);

        $listings = $this->searchListings($keywords);
        $categories = $this->searchCategories($keywords);

        if ($listings->isEmpty() && $categories->isEmpty()) {
            return null;
        }

        $sections = [];

        if ($listings->isNotEmpty()) {
            $sections[] = "Sản phẩm phù hợp:\n" . $listings->map(function (Listing $listing) {
                $price = number_format((float) $listing->price, 0, ',', '.');
                $category = $listing->category->name ?? 'Khác';
                $condition = Str::of($listing->condition ?? 'N/A')->replace('_', ' ')->upper();
                return sprintf('- #%d %s (%s) • %s đ • %s', $listing->id, $listing->title, $category, $price, $condition);
            })->implode("\n");
        }

        if ($categories->isNotEmpty()) {
            $sections[] = "Danh mục gợi ý:\n" . $categories->map(function (Category $category) {
                $count = $category->listings_count ?? 0;
                return sprintf('- %s • %d tin đang bán', $category->name, $count);
            })->implode("\n");
        }

        return implode("\n\n", $sections);
    }

    private function extractKeywords(string $message): array
    {
        $tokens = preg_split('/[^\p{L}\p{N}]+/u', Str::lower($message), -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            return [];
        }

        $filtered = collect($tokens)
            ->reject(function ($token) {
                return mb_strlen($token) <= 2 || in_array($token, $this->stopWords, true);
            })
            ->unique()
            ->values();

        return $filtered->take(6)->all();
    }

    private function searchListings(array $keywords)
    {
        $query = Listing::query()
            ->with('category')
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

    private function searchCategories(array $keywords)
    {
        $query = Category::query()
            ->withCount(['listings' => function ($q) {
                $q->where('status', 'approved');
            }])
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
}
