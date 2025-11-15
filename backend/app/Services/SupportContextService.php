<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
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
    public function buildContext(string $userMessage, ?array $excludeListingIds = null): array
    {
        $keywords = $this->extractKeywords($userMessage);
        $priceIntent = $this->detectPriceIntent($userMessage);
        $budgetIntent = $this->detectBudgetIntent($userMessage);
        $moreProductsIntent = $this->detectMoreProductsIntent($userMessage);
        $cardIntent = $this->determineCardIntent($userMessage, $priceIntent, $budgetIntent, $moreProductsIntent);
        
        // Log để debug
        try {
            Log::info('SupportContextService buildContext', [
                'user_message' => $userMessage,
                'keywords' => $keywords,
                'price_intent' => $priceIntent,
                'budget_intent' => $budgetIntent,
                'more_products_intent' => $moreProductsIntent,
                'card_intent' => $cardIntent,
            ]);
        } catch (\Throwable $e) {}

        // Tăng limit khi có budget intent, moreProductsIntent, hoặc price intent để tìm nhiều sản phẩm hơn
        // Đặc biệt với price intent "expensive", cần lấy nhiều hơn để đảm bảo có sản phẩm giá cao nhất
        $limit = ($moreProductsIntent || $budgetIntent) ? 20 : ($priceIntent === 'expensive' ? 50 : 10);
        
        // Khi có moreProductsIntent, không cần keywords - lấy tất cả sản phẩm khác
        $searchKeywords = $moreProductsIntent ? [] : $keywords;
        
        // Nếu không có keywords, budget, price intent, và moreProductsIntent → lấy sản phẩm mới nhất
        $shouldGetLatest = empty($keywords) && !$budgetIntent && !$priceIntent && !$moreProductsIntent;
        
        $listings = $this->searchListings($searchKeywords, $priceIntent, $budgetIntent, $limit, $excludeListingIds, $moreProductsIntent, $shouldGetLatest);
        
        // Log kết quả tìm được
        try {
            Log::info('buildContext - listings found', [
                'count' => $listings->count(),
                'has_budget' => !empty($budgetIntent),
                'has_keywords' => !empty($keywords),
                'listings_ids' => $listings->pluck('id')->toArray(),
            ]);
        } catch (\Throwable $e) {}
        $categories = $this->searchCategories($keywords);

        $sections = [];
        if ($listings->isNotEmpty()) {
            $sectionTitle = $moreProductsIntent 
                ? "Các sản phẩm khác trong kho (chưa được hiển thị trước đó):\n"
                : ($budgetIntent 
                    ? sprintf("Sản phẩm phù hợp với ngân sách %s:\n", $this->formatBudgetLabel($budgetIntent))
                    : "Sản phẩm phù hợp trong kho:\n");
            
            // Format danh sách sản phẩm với thông tin đầy đủ (KHÔNG hiển thị ID cho user)
            $productsList = $listings->map(function (Listing $listing) {
                $price = number_format((float) $listing->price, 0, ',', '.');
                $category = $listing->category->name ?? 'Khác';
                $condition = Str::of($listing->condition ?? 'N/A')->replace('_', ' ')->upper();
                $description = Str::limit($listing->description ?? '', 100);
                return sprintf(
                    "- Tên: %s | Danh mục: %s | Giá: %s đ | Tình trạng: %s%s",
                    $listing->title,
                    $category,
                    $price,
                    $condition,
                    $description ? " | Mô tả: " . $description : ""
                );
            })->implode("\n");
            
            $sections[] = $sectionTitle . $productsList . "\n\nLƯU Ý: CHỈ liệt kê các sản phẩm trên đây. KHÔNG được thêm bất kỳ sản phẩm nào khác.";
        } elseif ($moreProductsIntent) {
            // Nếu user hỏi "còn sản phẩm nào khác" nhưng không tìm thấy, thông báo rõ ràng
            $sections[] = "Hiện chưa có sản phẩm khác phù hợp trong kho. Bạn có thể thử tìm kiếm với từ khóa khác hoặc mở rộng tiêu chí tìm kiếm.";
        } elseif ($budgetIntent) {
            // Nếu có budget intent nhưng không tìm thấy sản phẩm
            $sections[] = sprintf(
                "Hiện chưa có sản phẩm nào phù hợp với ngân sách %s. Bạn có thể thử mở rộng khoảng giá hoặc tìm kiếm với từ khóa khác.",
                $this->formatBudgetLabel($budgetIntent)
            );
        }

        if ($priceIntent && $listings->isNotEmpty()) {
            // Đảm bảo sort đúng để lấy sản phẩm có giá cao nhất/thấp nhất
            // Với expensive, query đã sort DESC nên phần tử đầu tiên là giá cao nhất
            if ($priceIntent === 'expensive') {
                // Query đã sort DESC, nên first() là giá cao nhất
                $selected = $listings->first();
            } else {
                // Cheapest - sort ASC và lấy first
                $selected = $listings->sortBy('price')->first();
            }

            if ($selected) {
                $priceLabel = $priceIntent === 'cheapest' ? 'giá thấp nhất' : 'giá cao nhất';
                $sections[] = sprintf(
                    'Sản phẩm %s trong kho: %s • %s đ • %s',
                    $priceLabel,
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
                return sprintf('- %s • %s đ', $listing->title, $price);
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
            } elseif ($cardIntent['mode'] === 'list' || $budgetIntent || $moreProductsIntent) {
                // Khi có budget intent hoặc moreProductsIntent, hiển thị tất cả sản phẩm tìm được
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

    private function searchListings(array $keywords, ?string $priceIntent, ?array $budgetIntent, int $limit = 5, ?array $excludeListingIds = null, bool $moreProductsIntent = false, bool $shouldGetLatest = false): Collection
    {
        // Nếu là moreProductsIntent, bỏ qua keywords và lấy sản phẩm mới nhất
        if ($moreProductsIntent) {
            try {
                // Kiểm tra xem có quá nhiều sản phẩm đã show không
                $totalApproved = Listing::where('status', 'approved')->count();
                $excludeCount = $excludeListingIds ? count($excludeListingIds) : 0;
                
                Log::info('MoreProductsIntent detected', [
                    'total_approved' => $totalApproved,
                    'exclude_count' => $excludeCount,
                    'limit' => $limit,
                ]);
                
                // Nếu đã show > 70% tổng số sản phẩm, bỏ qua exclude để vẫn có kết quả
                $shouldExclude = $excludeListingIds && !empty($excludeListingIds) && ($totalApproved == 0 || ($excludeCount / $totalApproved) < 0.7);
                
                $query = $this->baseListingQuery($priceIntent, $limit);
                
                if ($shouldExclude) {
                    $query->whereNotIn('id', $excludeListingIds);
                }
                
                if ($budgetIntent) {
                    $this->applyBudgetFilter($query, $budgetIntent);
                }
                
                $results = $query->get();
                
                Log::info('MoreProductsIntent query result', [
                    'found_count' => $results->count(),
                    'should_exclude' => $shouldExclude,
                ]);
                
                // Nếu vẫn không có (có thể đã show hết), lấy bất kỳ sản phẩm nào (bỏ qua exclude)
                if ($results->isEmpty()) {
                    $fallbackQuery = Listing::query()
                        ->with([
                            'category',
                            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
                        ])
                        ->where('status', 'approved');
                    
                    // Không exclude nữa để đảm bảo có kết quả
                    if ($budgetIntent) {
                        $this->applyBudgetFilter($fallbackQuery, $budgetIntent);
                    }
                    
                    $results = $fallbackQuery->orderByDesc('approved_at')->limit($limit)->get();
                    
                    Log::info('MoreProductsIntent fallback result', [
                        'found_count' => $results->count(),
                    ]);
                }
                
                return $results;
            } catch (\Throwable $e) {
                Log::error('Error in searchListings with moreProductsIntent', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Fallback: trả về empty collection nếu có lỗi
                return collect();
            }
        }

        // Logic bình thường cho các trường hợp khác
        try {
            Log::info('searchListings - normal flow', [
                'keywords' => $keywords,
                'price_intent' => $priceIntent,
                'budget_intent' => $budgetIntent,
                'limit' => $limit,
                'should_get_latest' => $shouldGetLatest,
                'exclude_count' => $excludeListingIds ? count($excludeListingIds) : 0,
            ]);
            
            // Nếu shouldGetLatest, lấy sản phẩm mới nhất ngay lập tức
            if ($shouldGetLatest) {
                $results = $this->baseListingQuery(null, $limit)
                    ->when($excludeListingIds && !empty($excludeListingIds), fn($q) => $q->whereNotIn('id', $excludeListingIds))
                    ->get();
                
                Log::info('ShouldGetLatest - direct query result', [
                    'found_count' => $results->count(),
                ]);
                
                return $results;
            }
            
            $primaryQuery = $this->baseListingQuery($priceIntent, $limit);

            // Loại trừ các sản phẩm đã show trước đó
            if ($excludeListingIds && !empty($excludeListingIds)) {
                $primaryQuery->whereNotIn('id', $excludeListingIds);
            }

            // Nếu có budgetIntent, ưu tiên filter theo budget (có thể bỏ qua keywords nếu cần)
            if ($budgetIntent) {
                $this->applyBudgetFilter($primaryQuery, $budgetIntent);
                Log::info('Applied budget filter', ['budget' => $budgetIntent]);
            }

            // Chỉ apply keywords nếu có keywords
            if ($keywords && !empty($keywords)) {
                $this->applyKeywordFilter($primaryQuery, $keywords);
            }

            $results = $primaryQuery->get();
            
            Log::info('Primary query result', [
                'found_count' => $results->count(),
                'has_budget' => !empty($budgetIntent),
                'has_keywords' => !empty($keywords),
            ]);

            // Fallback: nếu không tìm thấy
            if ($results->isEmpty()) {
                // Nếu có budgetIntent, ưu tiên tìm theo budget (bỏ qua keywords và exclude nếu cần)
                if ($budgetIntent) {
                    // Kiểm tra xem có bao nhiêu sản phẩm phù hợp với budget trong DB
                    $checkQuery = Listing::where('status', 'approved');
                    $this->applyBudgetFilter($checkQuery, $budgetIntent);
                    $totalWithBudget = $checkQuery->count();
                    
                    Log::info('Budget fallback - checking total', [
                        'total_with_budget' => $totalWithBudget,
                        'budget' => $budgetIntent,
                    ]);
                    
                    $fallbackQuery = Listing::query()
                        ->with([
                            'category',
                            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
                        ])
                        ->where('status', 'approved');
                    
                    $this->applyBudgetFilter($fallbackQuery, $budgetIntent);
                    
                    // Nếu có ít sản phẩm phù hợp (< 10), không exclude để đảm bảo có kết quả
                    // Nếu có nhiều, mới exclude
                    $totalApproved = Listing::where('status', 'approved')->count();
                    $excludeCount = $excludeListingIds ? count($excludeListingIds) : 0;
                    $shouldExclude = $excludeListingIds && !empty($excludeListingIds) 
                        && $totalWithBudget > 10 
                        && ($totalApproved == 0 || ($excludeCount / $totalApproved) < 0.7);
                    
                    if ($shouldExclude) {
                        $fallbackQuery->whereNotIn('id', $excludeListingIds);
                    }
                    
                    if ($priceIntent === 'cheapest') {
                        $fallbackQuery->orderBy('price', 'asc');
                    } elseif ($priceIntent === 'expensive') {
                        $fallbackQuery->orderBy('price', 'desc');
                    } else {
                        $fallbackQuery->orderByDesc('approved_at');
                    }
                    
                    $results = $fallbackQuery->limit($limit)->get();
                    
                    Log::info('Budget fallback query result', [
                        'found_count' => $results->count(),
                        'total_with_budget' => $totalWithBudget,
                        'should_exclude' => $shouldExclude,
                    ]);
                } else {
                    // Fallback cho trường hợp không có budgetIntent
                    $fallbackQuery = $this->baseListingQuery($priceIntent, $limit);
                    
                    if ($excludeListingIds && !empty($excludeListingIds)) {
                        $fallbackQuery->whereNotIn('id', $excludeListingIds);
                    }
                    
                    // Nếu có keywords nhưng không match, thử tìm với keyword đầu tiên
                    if ($keywords && count($keywords) > 0) {
                        $this->applyKeywordFilter($fallbackQuery, [$keywords[0]]);
                    }
                    
                    $results = $fallbackQuery->get();
                    
                    // Nếu vẫn không có kết quả và không có keywords, lấy sản phẩm mới nhất
                    if ($results->isEmpty() && empty($keywords)) {
                        $results = $this->baseListingQuery($priceIntent, $limit)
                            ->when($excludeListingIds && !empty($excludeListingIds), fn($q) => $q->whereNotIn('id', $excludeListingIds))
                            ->get();
                    }
                }
            }
            
            // Nếu shouldGetLatest và không có kết quả, lấy sản phẩm mới nhất
            if ($shouldGetLatest && $results->isEmpty()) {
                $results = $this->baseListingQuery(null, $limit)
                    ->when($excludeListingIds && !empty($excludeListingIds), fn($q) => $q->whereNotIn('id', $excludeListingIds))
                    ->get();
                
                Log::info('ShouldGetLatest fallback result', [
                    'found_count' => $results->count(),
                ]);
            }

            return $results;
        } catch (\Throwable $e) {
            Log::error('Error in searchListings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return collect();
        }
    }

    private function baseListingQuery(?string $priceIntent, int $limit = 5)
    {
        $query = Listing::query()
            ->with([
                'category',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            ])
            ->where('status', 'approved');

        // Sort theo price intent - QUAN TRỌNG: phải sort trước khi limit
        if ($priceIntent === 'cheapest') {
            $query->orderBy('price', 'asc');
        } elseif ($priceIntent === 'expensive') {
            // Đảm bảo lấy sản phẩm có giá cao nhất - sort DESC
            $query->orderBy('price', 'desc');
        } else {
            // Mặc định: sản phẩm mới nhất
            $query->orderByDesc('approved_at');
        }
        
        // Apply limit sau khi sort để đảm bảo lấy đúng sản phẩm
        $query->limit($limit);

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

        $cheapestSignals = [
            're nhat', 'gia re nhat', 'thap nhat', 'gia thap', 'it tien', 're hon', 
            'gia thap nhat', 're nhat di', 're nhat o day', 're nhat', 'gia re',
            're', 'rẻ nhất', 'giá rẻ nhất', 'thấp nhất', 'giá thấp'
        ];
        foreach ($cheapestSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return 'cheapest';
            }
        }

        $expensiveSignals = [
            'dat nhat', 'mac nhat', 'cao nhat', 'gia cao', 'gia mac', 'dat tien',
            'mắc nhất', 'đắt nhất', 'giá mắc nhất', 'giá đắt nhất', 'giá cao nhất',
            'mắc', 'đắt', 'cao nhất', 'giá mắc', 'giá đắt', 'giá cao',
            'expensive', 'highest price', 'most expensive'
        ];
        foreach ($expensiveSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return 'expensive';
            }
        }

        return null;
    }

    private function detectMoreProductsIntent(string $message): bool
    {
        $normalized = Str::lower(Str::ascii($message));
        
        // Thêm nhiều patterns hơn để detect "còn gì khác"
        $moreSignals = [
            'con san pham', 'còn sản phẩm', 'con nao', 'còn nào', 'khac', 'khác',
            'them san pham', 'thêm sản phẩm', 'them nua', 'thêm nữa',
            'san pham khac', 'sản phẩm khác', 'lua chon khac', 'lựa chọn khác',
            'co gi khac', 'có gì khác', 'co san pham nao khac', 'có sản phẩm nào khác',
            'con gi', 'còn gì', 'con gi khac', 'còn gì khác', 'con gi nua', 'còn gì nữa',
            'con gi khong', 'còn gì không', 'con gi khac khong', 'còn gì khác không',
            'co gi nua', 'có gì nữa', 'co gi khac khong', 'có gì khác không',
            'more products', 'other products', 'another', 'more options', 'anything else',
            'what else', 'more', 'others', 'different'
        ];
        
        foreach ($moreSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return true;
            }
        }
        
        return false;
    }

    private function determineCardIntent(string $message, ?string $priceIntent, ?array $budgetIntent, bool $moreProductsIntent = false): array
    {
        // Khi có budget intent, luôn hiển thị danh sách sản phẩm
        if ($priceIntent || $budgetIntent) {
            return ['include' => true, 'mode' => $budgetIntent ? 'list' : 'single'];
        }

        // Nếu user hỏi "còn sản phẩm nào khác", luôn hiển thị danh sách
        if ($moreProductsIntent) {
            return ['include' => true, 'mode' => 'list'];
        }

        $normalized = Str::lower(Str::ascii($message));

        $listSignals = ['liet ke', 'danh sach', 'cho toi vai', 'goi y', 'de xuat', 'mot vai', 'several', 'list cho', 'cho minh vai san pham', 'nhieu san pham', 'nhiều sản phẩm'];
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

        // Range: "tu 3 tr den 5 tr", "khoang 2-4tr", "gia tu 3 den 5 trieu"
        if (preg_match('/(?:gia\s+)?(?:tu|khoang|giua)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?\s*(?:den|toi|\-|to)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?/', $normalized, $m)) {
            $min = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            $max = $this->convertBudgetToVnd($m[3], $m[4] ?? $m[2] ?? null);
            if ($min && $max && $min < $max) {
                return ['min' => $min, 'max' => $max];
            }
        }

        // "duoi 5 trieu", "gia duoi 5 trieu", "co gia duoi 5 trieu", "gia nho hon 5 trieu"
        if (preg_match('/(?:co\s+)?(?:gia\s+)?(?:duoi|nho hon|thap hon|<=|<|toi da|khong qua|be hon)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?/', $normalized, $m)) {
            $max = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            if ($max) {
                return ['max' => $max];
            }
        }

        // "gia (\d+) tro lai", "(\d+) trieu tro lai"
        if (preg_match('/(?:gia\s+)?(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?\s*(?:tro lai|nguoc lai)/', $normalized, $m)) {
            $max = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            if ($max) {
                return ['max' => $max];
            }
        }

        // "tren 5 trieu", "gia tren 5 trieu", "co gia tren 5 trieu", "gia lon hon 5 trieu"
        if (preg_match('/(?:co\s+)?(?:gia\s+)?(?:tren|lon hon|cao hon|>=|>|toi thieu|it nhat)\s*(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)?/', $normalized, $m)) {
            $min = $this->convertBudgetToVnd($m[1], $m[2] ?? null);
            if ($min) {
                return ['min' => $min];
            }
        }

        // Pattern đơn giản: "5 trieu", "5tr" (nếu có từ "gia" hoặc "duoi" gần đó)
        if (preg_match('/(?:gia|duoi|tren|nho hon|lon hon).*?(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)/', $normalized, $m)) {
            // Nếu có từ "duoi" hoặc "nho hon" trước số -> max
            if (preg_match('/(?:duoi|nho hon|thap hon|be hon).*?(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)/', $normalized, $m2)) {
                $max = $this->convertBudgetToVnd($m2[1], $m2[2] ?? null);
                if ($max) {
                    return ['max' => $max];
                }
            }
            // Nếu có từ "tren" hoặc "lon hon" trước số -> min
            if (preg_match('/(?:tren|lon hon|cao hon).*?(\d+(?:\.\d+)?)\s*(tr|trieu|k|ngan|nghin|d|dong|vn?d)/', $normalized, $m2)) {
                $min = $this->convertBudgetToVnd($m2[1], $m2[2] ?? null);
                if ($min) {
                    return ['min' => $min];
                }
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
