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
        'là','và','hay','hoặc','được','duoc','gì','gi','các','những','một','muốn','nhờ','nhé','nhỉ','oi','ơi','thanks','cảm','ơn',
        // generic product words & price qualifiers (loại bỏ khi trích keyword)
        'san','sản','pham','phẩm','nao','nào','gia','giá','tam','tầm','voi','với','trieu','triệu','tr','tro','trở','lai','lại',
        'thi','thì','nen','nên','mua','hang','hàng','trong','cua','cửa','ngan','ngàn','nghin','nghìn','tien','tiền',
        'thap','thấp','re','rẻ','cao','dat','đắt','mac','mắc','nhat','nhất'
    ];
    private array $normalizedStopWords;

    public function __construct()
    {
        $this->normalizedStopWords = collect($this->stopWords)
            ->map(fn ($word) => Str::lower(Str::ascii($word)))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array{context:?string,products:array<int,array<string,mixed>>}
     */
    public function buildContext(string $userMessage): array
    {
        $keywords = $this->extractKeywords($userMessage);
        $priceIntent = $this->detectPriceIntent($userMessage);
        $budgetIntent = $this->detectBudgetIntent($userMessage);
        $cardIntent = $this->determineCardIntent($userMessage, $priceIntent, $budgetIntent);

        $listings = $this->searchListings($keywords, $priceIntent, $budgetIntent);
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

        if ($priceIntent && $listings->isNotEmpty()) {
            $selected = $priceIntent === 'cheapest'
                ? $listings->sortBy('price')->first()
                : $listings->sortByDesc('price')->first();

            if ($selected) {
                $priceLabel = $priceIntent === 'cheapest' ? 'giá thấp nhất' : 'giá cao nhất';
                $sections[] = sprintf(
                    'Sản phẩm %s tìm được: #%d %s • %s đ • %s',
                    $priceLabel,
                    $selected->id,
                    $selected->title,
                    number_format((float) $selected->price, 0, ',', '.'),
                    $selected->category->name ?? 'Khác'
                );
            }
        }

        if ($budgetIntent && $listings->isNotEmpty()) {
            $sections[] = sprintf(
                'Sản phẩm phù hợp cho ngân sách %s:',
                $this->formatBudgetLabel($budgetIntent)
            ) . "\n" . $listings->map(function (Listing $listing) {
                $price = number_format((float) $listing->price, 0, ',', '.');
                return sprintf('- #%d %s • %s đ', $listing->id, $listing->title, $price);
            })->implode("\n");
        }

        if ($categories->isNotEmpty()) {
            $sections[] = "Danh mục gợi ý nổi bật:\n" . $categories->map(function (Category $category) {
                $count = $category->listings_count ?? 0;
                return sprintf('- %s • %d tin đang bán', $category->name, $count);
            })->implode("\n");
        }

        $productsForCards = collect();
        if ($cardIntent['include'] && $listings->isNotEmpty()) {
            if ($priceIntent) {
                $target = $priceIntent === 'cheapest'
                    ? $listings->sortBy('price')->first()
                    : $listings->sortByDesc('price')->first();
                $productsForCards = $target ? collect([$target]) : collect();
            } elseif ($cardIntent['mode'] === 'list' || $budgetIntent) {
                $productsForCards = $listings;
            } else {
                $productsForCards = $listings->take(1);
            }
        }

        return [
            'context' => $sections ? implode("\n\n", $sections) : null,
            'products' => $this->formatProductCards($productsForCards),
        ];
    }

    private function extractKeywords(string $message): array
    {
        $tokens = preg_split('/[^\p{L}\p{N}]+/u', Str::lower($message), -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            return [];
        }

        $normalizedLookup = $this->normalizedStopWords;

        return collect($tokens)
            ->reject(function ($token) use ($normalizedLookup) {
                if (mb_strlen($token) <= 2) {
                    return true;
                }
                $normalized = Str::lower(Str::ascii($token));
                return in_array($normalized, $normalizedLookup, true);
            })
            ->unique()
            ->values()
            ->take(6)
            ->all();
    }

    private function searchListings(array $keywords, ?string $priceIntent, ?array $budgetIntent): Collection
    {
        $primaryQuery = $this->baseListingQuery($priceIntent);

        if ($keywords) {
            $this->applyKeywordFilter($primaryQuery, $keywords);
        }

        if ($budgetIntent) {
            $this->applyBudgetFilter($primaryQuery, $budgetIntent);
        }

        $results = $primaryQuery->get();

        if ($results->isEmpty() && ($keywords || $budgetIntent)) {
            $fallbackQuery = $this->baseListingQuery($priceIntent);
            if ($keywords) {
                $this->applyKeywordFilter($fallbackQuery, $keywords);
            }
            if ($budgetIntent) {
                $this->applyBudgetFilter($fallbackQuery, $budgetIntent);
            }
            $results = $fallbackQuery->get();
        }

        return $results;
    }

    private function baseListingQuery(?string $priceIntent)
    {
        $query = Listing::query()
            ->with([
                'category',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            ])
            ->where('status', 'approved')
            ->limit(5);

        if ($priceIntent === 'cheapest') {
            $query->orderBy('price', 'asc');
        } elseif ($priceIntent === 'expensive') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderByDesc('approved_at');
        }

        return $query;
    }

    private function applyKeywordFilter($query, array $keywords): void
    {
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->orWhere('title', 'like', "%{$word}%")
                  ->orWhere('description', 'like', "%{$word}%");
            }
        });
    }

    private function detectPriceIntent(string $message): ?string
    {
        $normalized = Str::lower(Str::ascii($message));

        $cheapestSignals = ['re nhat', 'gia re nhat', 'thap nhat', 'gia thap', 'it tien', 're hon', 'gia thap nhat', 're nhat di', 're nhat o day'];
        foreach ($cheapestSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return 'cheapest';
            }
        }

        $expensiveSignals = ['dat nhat', 'mac nhat', 'cao nhat', 'gia cao', 'gia mac', 'cao nhat', 'dat tien'];
        foreach ($expensiveSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return 'expensive';
            }
        }

        return null;
    }

    private function determineCardIntent(string $message, ?string $priceIntent, ?array $budgetIntent): array
    {
        if ($priceIntent || $budgetIntent) {
            return ['include' => true, 'mode' => 'single'];
        }

        $normalized = Str::lower(Str::ascii($message));

        $listSignals = ['liet ke', 'danh sach', 'cho toi vai', 'goi y', 'de xuat', 'mot vai', 'several', 'list cho', 'cho minh vai san pham'];
        foreach ($listSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return ['include' => true, 'mode' => 'list'];
            }
        }

        $singleSignals = ['san pham cu the', 'gui link', 'link san pham', 'card san pham', 'nhan vao san pham', 'xem chi tiet', 'cho toi san pham', 'the hien card'];
        foreach ($singleSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return ['include' => true, 'mode' => 'single'];
            }
        }

        return ['include' => false, 'mode' => null];
    }

    private function detectBudgetIntent(string $message): ?array
    {
        $normalized = Str::lower(Str::ascii($message));
        $normalized = str_replace([','], '.', $normalized);

        // Range: "tu 3 tr den 5 tr", "khoang 2-4tr"
        if (preg_match('/(?:tu|khoang|giua)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?\s*(?:den|toi|\-|to)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?/', $normalized, $m)) {
            $min = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            $max = $this->convertBudgetToVnd($m[3], $m[4] ?? $m[2] ?? null);
            if ($min && $max && $min < $max) {
                return ['min' => $min, 'max' => $max];
            }
        }

        if (preg_match('/(?:duoi|nho hon|<=|<|toi da|khong qua)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?/', $normalized, $m)) {
            $max = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            if ($max) {
                return ['max' => $max];
            }
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?\s*(?:tro lai|nguoc lai)/', $normalized, $m)) {
            $max = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            if ($max) {
                return ['max' => $max];
            }
        }

        if (preg_match('/(?:tren|lon hon|>=|>|toi thieu|it nhat)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?/', $normalized, $m)) {
            $min = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            if ($min) {
                return ['min' => $min];
            }
        }

        return null;
    }

    private function convertBudgetToVnd(string $value, ?string $unit): ?int
    {
        $num = (float) $value;
        $unit = trim($unit ?? '');

        if ($num <= 0) {
            return null;
        }

        $unitMap = [
            'tr' => 1_000_000,
            'trieu' => 1_000_000,
            'k' => 1_000,
            'ngan' => 1_000,
            'nghin' => 1_000,
            'd' => 1,
            'dong' => 1,
            'vnd' => 1,
            'vn' => 1,
        ];

        $multiplier = $unitMap[$unit] ?? 1;

        return (int) round($num * $multiplier);
    }

    private function applyBudgetFilter($query, array $budget): void
    {
        if (!empty($budget['min'])) {
            $query->where('price', '>=', (int) $budget['min']);
        }
        if (!empty($budget['max'])) {
            $query->where('price', '<=', (int) $budget['max']);
        }
    }

    private function formatBudgetLabel(array $budget): string
    {
        $format = fn (int $vnd) => number_format($vnd, 0, ',', '.'). ' đ';

        if (!empty($budget['min']) && !empty($budget['max'])) {
            return sprintf('từ %s đến %s', $format($budget['min']), $format($budget['max']));
        }
        if (!empty($budget['min'])) {
            return sprintf('từ %s trở lên', $format($budget['min']));
        }
        if (!empty($budget['max'])) {
            return sprintf('dưới %s', $format($budget['max']));
        }

        return 'được yêu cầu';
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
