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
    public function buildContext(string $userMessage, ?array $excludeListingIds = null, ?array $conversationHistory = null): array
    {
        // Kiểm tra xem câu hỏi có liên quan đến sản phẩm không
        // Nếu chỉ là chào hỏi, tương tác thông thường → không query DB
        $isProductRelated = $this->isProductRelatedQuery($userMessage);
        $keywords = [];
        
        // Nếu không phải product-related, nhưng có thể là câu trả lời ngắn (có, muốn, cho tôi xem)
        // hoặc yêu cầu xem sản phẩm, thì thử lấy keyword từ conversation history
        if (!$isProductRelated && $conversationHistory) {
            $normalized = Str::lower(Str::ascii(trim($userMessage)));
            
            // Pattern cho câu trả lời ngắn
            $shortResponsePatterns = ['^co$', '^có$', '^muon$', '^muốn$', '^cho toi$', '^cho tôi$', '^xem$', '^yes$', '^ok$'];
            // Pattern cho yêu cầu xem sản phẩm
            $viewProductPatterns = [
                'cho toi xem', 'cho tôi xem', 'cho xem', 'xem san pham', 'xem sản phẩm',
                'hien thi', 'hiển thị', 'show', 'liet ke', 'liệt kê',
                'danh sach', 'danh sách', 'list'
            ];
            
            $isShortResponse = false;
            foreach ($shortResponsePatterns as $pattern) {
                if (preg_match('/' . $pattern . '/iu', $normalized)) {
                    $isShortResponse = true;
                    break;
                }
            }
            
            $isViewRequest = false;
            foreach ($viewProductPatterns as $pattern) {
                if (Str::contains($normalized, $pattern)) {
                    $isViewRequest = true;
                    break;
                }
            }
            
            // Nếu là câu trả lời ngắn hoặc yêu cầu xem, thử lấy keyword từ message trước
            if ($isShortResponse || $isViewRequest) {
                // Tìm message user gần nhất có keyword, hoặc message AI gần nhất có mention danh mục
                foreach (array_reverse($conversationHistory) as $msg) {
                    if (isset($msg['role']) && $msg['role'] === 'user' && !empty($msg['content'])) {
                        $prevKeywords = $this->extractKeywords($msg['content']);
                        if (!empty($prevKeywords)) {
                            // Sử dụng keyword từ message trước
                            $keywords = $prevKeywords;
                            $isProductRelated = true;
                            break;
                        }
                    } elseif (isset($msg['role']) && $msg['role'] === 'assistant' && !empty($msg['content'])) {
                        // Nếu không tìm thấy từ user message, thử extract từ AI message (có thể mention danh mục)
                        $prevKeywords = $this->extractKeywords($msg['content']);
                        if (!empty($prevKeywords)) {
                            $keywords = $prevKeywords;
                            $isProductRelated = true;
                            break;
                        }
                    }
                }
            }
        }
        
        if (!$isProductRelated) {
            return [
                'context' => null,
                'products' => [],
            ];
        }
        
        // Nếu chưa có keywords (từ conversation history), extract từ message hiện tại
        if (empty($keywords)) {
            $keywords = $this->extractKeywords($userMessage);
        }
        $priceIntent = $this->detectPriceIntent($userMessage);
        $budgetIntent = $this->detectBudgetIntent($userMessage);
        $moreProductsIntent = $this->detectMoreProductsIntent($userMessage);
        $listAllProductsIntent = $this->detectListAllProductsIntent($userMessage);
        $cardIntent = $this->determineCardIntent($userMessage, $priceIntent, $budgetIntent, $moreProductsIntent, $listAllProductsIntent);
        
        // Log để debug
        try {
            Log::info('SupportContextService buildContext', [
                'user_message' => $userMessage,
                'keywords' => $keywords,
                'price_intent' => $priceIntent,
                'budget_intent' => $budgetIntent,
                'more_products_intent' => $moreProductsIntent,
                'list_all_products_intent' => $listAllProductsIntent,
                'card_intent' => $cardIntent,
            ]);
        } catch (\Throwable $e) {}

        // Khi user hỏi "có những sản phẩm gì" (listAllProductsIntent), KHÔNG query listings ngay
        // Thay vào đó, chỉ query categories và để AI hỏi lại user về danh mục họ quan tâm
        if ($listAllProductsIntent) {
            // Lấy tất cả danh mục có sản phẩm (không cần keywords)
            $categories = $this->getAllCategoriesWithProducts();
            
            // Không query listings, chỉ trả về categories để AI hỏi lại user
            $sections = [];
            if ($categories->isNotEmpty()) {
                $categoriesList = $categories->map(function (Category $category) {
                    $count = $category->listings_count ?? 0;
                    return sprintf('- %s (%d sản phẩm)', $category->name, $count);
                })->implode("\n");
                
                $sections[] = "Danh sách các danh mục hiện có trên trang web:\n" . $categoriesList . 
                    "\n\nLƯU Ý QUAN TRỌNG: Người dùng đang hỏi về sản phẩm nói chung. Bạn PHẢI:" .
                    "\n1. Hỏi lại người dùng: 'Bạn đang quan tâm đến danh mục nào?'" .
                    "\n2. Liệt kê các danh mục trên đây để người dùng lựa chọn" .
                    "\n3. Đợi người dùng chọn danh mục trước khi giới thiệu sản phẩm" .
                    "\n4. KHÔNG được tự động liệt kê sản phẩm ngay bây giờ";
            } else {
                $sections[] = "Hiện chưa có danh mục nào trong hệ thống.";
            }
            
            return [
                'context' => implode("\n\n", $sections),
                'products' => [],
            ];
        }
        
        // Logic bình thường cho các trường hợp khác
        // Tăng limit khi có budget intent, moreProductsIntent, hoặc price intent để tìm nhiều sản phẩm hơn
        // Đặc biệt với price intent "expensive", cần lấy nhiều hơn để đảm bảo có sản phẩm giá cao nhất
        $limit = ($moreProductsIntent || $budgetIntent) ? 20 : ($priceIntent === 'expensive' ? 50 : 10);
        
        // Khi có moreProductsIntent, không cần keywords - lấy tất cả sản phẩm khác
        $searchKeywords = $moreProductsIntent ? [] : $keywords;
        
        // Nếu không có keywords, budget, price intent, và moreProductsIntent → lấy sản phẩm mới nhất
        $shouldGetLatest = empty($keywords) && !$budgetIntent && !$priceIntent && !$moreProductsIntent;
        
        $listings = $this->searchListings($searchKeywords, $priceIntent, $budgetIntent, $limit, $excludeListingIds, $moreProductsIntent, $shouldGetLatest, false);
        
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
                : ($listAllProductsIntent
                    ? "Danh sách sản phẩm hiện có trong kho:\n"
                    : ($budgetIntent 
                        ? sprintf("Sản phẩm phù hợp với ngân sách %s:\n", $this->formatBudgetLabel($budgetIntent))
                        : "Sản phẩm phù hợp trong kho:\n"));
            
            // Format danh sách sản phẩm với thông tin đầy đủ (bao gồm URL để AI có thể gửi link khi user yêu cầu)
            $productsList = $listings->map(function (Listing $listing) {
                $price = number_format((float) $listing->price, 0, ',', '.');
                $category = $listing->category->name ?? 'Khác';
                $condition = Str::of($listing->condition ?? 'N/A')->replace('_', ' ')->upper();
                $description = Str::limit($listing->description ?? '', 100);
                $url = '/listings/' . $listing->id;
                return sprintf(
                    "- Tên: %s | Danh mục: %s | Giá: %s đ | Tình trạng: %s | Link: %s%s",
                    $listing->title,
                    $category,
                    $price,
                    $condition,
                    $url,
                    $description ? " | Mô tả: " . $description : ""
                );
            })->implode("\n");
            
            $sections[] = $sectionTitle . $productsList . 
                "\n\nLƯU Ý QUAN TRỌNG:" .
                "\n- Bạn PHẢI liệt kê TẤT CẢ các sản phẩm trên đây ngay lập tức, không chỉ nói số lượng." .
                "\n- CHỈ được liệt kê các sản phẩm trên đây. KHÔNG được thêm bất kỳ sản phẩm nào khác." .
                "\n- Khi gợi ý sản phẩm, hệ thống sẽ tự động hiển thị card sản phẩm để người dùng dễ nhấn vào xem chi tiết." .
                "\n- Khi người dùng yêu cầu link sản phẩm: Hệ thống sẽ tự động hiển thị card sản phẩm. Bạn KHÔNG cần gửi text link trong tin nhắn, chỉ cần nói rằng card sản phẩm đã được hiển thị và người dùng có thể nhấn vào để xem chi tiết." .
                "\n- Khi người dùng hỏi 'cho tôi xem', 'xem sản phẩm', 'liệt kê': PHẢI liệt kê tất cả sản phẩm trên đây, không chỉ nói số lượng.";
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
                $url = '/listings/' . $selected->id;
                $sections[] = sprintf(
                    'Sản phẩm %s trong kho: %s • %s đ • %s • Link: %s',
                    $priceLabel,
                    $selected->title,
                    number_format((float) $selected->price, 0, ',', '.'),
                    $selected->category->name ?? 'Khác',
                    $url
                );
            }
        }

        if ($budgetIntent && $listings->isNotEmpty()) {
            $sections[] = sprintf(
                'Sản phẩm phù hợp cho ngân sách %s:',
                $this->formatBudgetLabel($budgetIntent)
            ) . "\n" . $listings->map(function (Listing $listing) {
                $price = number_format((float) $listing->price, 0, ',', '.');
                $url = '/listings/' . $listing->id;
                return sprintf('- %s • %s đ • Link: %s', $listing->title, $price, $url);
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
            } elseif ($cardIntent['mode'] === 'list' || $budgetIntent || $moreProductsIntent || $listAllProductsIntent) {
                // Khi có budget intent, moreProductsIntent, hoặc listAllProductsIntent, hiển thị tất cả sản phẩm tìm được
                $productsForCards = $listings;
            } elseif ($cardIntent['mode'] === 'single') {
                // Khi user yêu cầu single product hoặc link
                $productsForCards = $listings->take(1);
            } elseif ($cardIntent['mode'] === 'auto') {
                // Mặc định: hiển thị tối đa 5 sản phẩm để không quá dài
                $productsForCards = $listings->take(5);
            } else {
                // Fallback: hiển thị 1 sản phẩm
                $productsForCards = $listings->take(1);
            }
        }

        return [
            'context' => $sections ? implode("\n\n", $sections) : null,
            'products' => $this->formatProductCards($productsForCards),
        ];
    }

    /**
     * Kiểm tra xem câu hỏi có liên quan đến sản phẩm không
     * Nếu chỉ là chào hỏi, tương tác thông thường → return false
     */
    private function isProductRelatedQuery(string $message): bool
    {
        $normalized = Str::lower(Str::ascii($message));
        $trimmed = trim($normalized);
        
        // Các câu chào hỏi, tương tác thông thường - KHÔNG liên quan đến sản phẩm
        // Lưu ý: "có", "muốn", "cho tôi xem" có thể là câu trả lời về sản phẩm, không loại bỏ ở đây
        $greetingPatterns = [
            '^alo$', '^hello$', '^hi$', '^chao$', '^chào$', '^xin chao$', '^xin chào$',
            '^alo e$', '^alo ban$', '^alo bạn$', '^chao ban$', '^chào bạn$',
            '^hi ban$', '^hi bạn$', '^hello ban$', '^hello bạn$',
            '^cam on$', '^cảm ơn$', '^thanks$', '^thank you$',
            '^bye$', '^tam biet$', '^tạm biệt$', '^chao tam biet$', '^chào tạm biệt$',
        ];
        
        foreach ($greetingPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/iu', $trimmed)) {
                return false;
            }
        }
        
        // Nếu câu quá ngắn (< 3 ký tự) và không có từ khóa sản phẩm → không liên quan
        if (mb_strlen($trimmed) < 3) {
            return false;
        }
        
        // Các từ khóa chỉ ra câu hỏi về sản phẩm
        $productKeywords = [
            'san pham', 'sản phẩm', 'product', 'hang', 'hàng', 'item',
            'gia', 'giá', 'price', 'mua', 'ban', 'bán', 'sell', 'buy',
            'laptop', 'sach', 'sách', 'book', 'may tinh', 'máy tính',
            'tim', 'tìm', 'search', 'co', 'có', 'have', 'list', 'danh sach', 'danh sách',
            're', 'rẻ', 'dat', 'đắt', 'mắc', 'cheap', 'expensive',
            'trieu', 'triệu', 'million', 'ngan', 'ngàn', 'thousand',
            'danh muc', 'danh mục', 'category', 'loai', 'loại', 'type',
            'tin rao', 'listing', 'offer', 'de nghi', 'đề nghị',
        ];
        
        foreach ($productKeywords as $keyword) {
            if (Str::contains($normalized, $keyword)) {
                return true;
            }
        }
        
        // Nếu có số (có thể là giá) → có thể liên quan đến sản phẩm
        if (preg_match('/\d+/', $normalized)) {
            // Kiểm tra xem số có đi kèm với từ khóa giá không
            if (preg_match('/(?:gia|giá|price|trieu|triệu|tr|ngan|ngàn|nghin|nghìn|d|dong|đồng|vnd)/iu', $normalized)) {
                return true;
            }
            // Nếu chỉ có số đơn thuần, có thể không liên quan đến sản phẩm
        }
        
        // Kiểm tra các intent nhanh (không cần gọi full method)
        $normalizedForIntent = $normalized;
        
        // Quick check cho price intent
        $priceSignals = ['re nhat', 'dat nhat', 'mac nhat', 'cao nhat', 'thap nhat', 'mắc nhất', 'đắt nhất', 'rẻ nhất', 'thấp nhất'];
        foreach ($priceSignals as $signal) {
            if (Str::contains($normalizedForIntent, $signal)) {
                return true;
            }
        }
        
        // Quick check cho budget intent (có số + từ khóa giá)
        if (preg_match('/\d+\s*(tr|trieu|ngan|nghin|d|dong|vnd)/iu', $normalizedForIntent)) {
            return true;
        }
        
        // Quick check cho more products intent
        $moreSignals = ['con san pham', 'còn sản phẩm', 'con gi khac', 'còn gì khác', 'them san pham', 'thêm sản phẩm'];
        foreach ($moreSignals as $signal) {
            if (Str::contains($normalizedForIntent, $signal)) {
                return true;
            }
        }
        
        // Nếu có keywords được extract (sau khi loại bỏ stop words) → có thể liên quan
        $keywords = $this->extractKeywords($message);
        if (!empty($keywords)) {
            return true;
        }
        
        // Mặc định: nếu không chắc, coi như không liên quan (để AI trả lời tự nhiên)
        return false;
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

    private function searchListings(array $keywords, ?string $priceIntent, ?array $budgetIntent, int $limit = 5, ?array $excludeListingIds = null, bool $moreProductsIntent = false, bool $shouldGetLatest = false, bool $listAllProductsIntent = false): Collection
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
            
            // Nếu shouldGetLatest hoặc listAllProductsIntent, lấy sản phẩm mới nhất ngay lập tức
            if ($shouldGetLatest || $listAllProductsIntent) {
                $query = $this->baseListingQuery(null, $limit);
                
                // Với listAllProductsIntent, nếu có quá nhiều sản phẩm đã show (> 70%), bỏ qua exclude để đảm bảo có kết quả
                if ($listAllProductsIntent && $excludeListingIds && !empty($excludeListingIds)) {
                    $totalApproved = Listing::where('status', 'approved')->count();
                    $excludeCount = count($excludeListingIds);
                    $shouldExclude = $totalApproved > 0 && ($excludeCount / $totalApproved) < 0.7;
                    
                    if ($shouldExclude) {
                        $query->whereNotIn('id', $excludeListingIds);
                    }
                } elseif ($excludeListingIds && !empty($excludeListingIds)) {
                    $query->whereNotIn('id', $excludeListingIds);
                }
                
                $results = $query->get();
                
                Log::info('ShouldGetLatest/ListAllProductsIntent - direct query result', [
                    'found_count' => $results->count(),
                    'list_all_products_intent' => $listAllProductsIntent,
                ]);
                
                // Nếu listAllProductsIntent và không có kết quả, thử lại không exclude
                if ($listAllProductsIntent && $results->isEmpty() && $excludeListingIds && !empty($excludeListingIds)) {
                    $results = $this->baseListingQuery(null, $limit)->get();
                    Log::info('ListAllProductsIntent - fallback without exclude', [
                        'found_count' => $results->count(),
                    ]);
                }
                
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
        // Tìm các category có tên match với keywords
        $matchingCategoryIds = Category::query()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('name', 'like', "%{$word}%");
                }
            })
            ->pluck('id')
            ->toArray();

        $query->where(function ($q) use ($keywords, $matchingCategoryIds) {
            // Tìm trong title và description
            foreach ($keywords as $word) {
                $q->orWhere('title', 'like', "%{$word}%")
                  ->orWhere('description', 'like', "%{$word}%");
            }
            
            // Nếu có category match, cũng tìm theo category_id
            if (!empty($matchingCategoryIds)) {
                $q->orWhereIn('category_id', $matchingCategoryIds);
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

    /**
     * Detect khi user hỏi "có những sản phẩm gì", "danh sách sản phẩm", "trang web có gì"
     * Khác với moreProductsIntent (hỏi "còn gì khác"), đây là hỏi lần đầu về tất cả sản phẩm
     */
    private function detectListAllProductsIntent(string $message): bool
    {
        $normalized = Str::lower(Str::ascii($message));
        
        // Các patterns để detect "có những sản phẩm gì", "danh sách sản phẩm"
        $listAllSignals = [
            'co nhung san pham', 'có những sản phẩm', 'co nhung san pham gi', 'có những sản phẩm gì',
            'trang web co nhung san pham', 'trang web có những sản phẩm', 'trang web co gi', 'trang web có gì',
            'website co nhung san pham', 'website có những sản phẩm', 'website co gi', 'website có gì',
            'co nhung san pham nao', 'có những sản phẩm nào', 'co san pham nao', 'có sản phẩm nào',
            'danh sach san pham', 'danh sách sản phẩm', 'list san pham', 'list sản phẩm',
            'hien thi san pham', 'hiển thị sản phẩm', 'show products', 'show san pham',
            'co gi ban', 'có gì bán', 'ban gi', 'bán gì', 'co gi de ban', 'có gì để bán',
            'what products', 'what items', 'list products', 'show products', 'what do you have',
            'what are the products', 'what products are available', 'what items are available',
        ];
        
        foreach ($listAllSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return true;
            }
        }
        
        return false;
    }

    private function determineCardIntent(string $message, ?string $priceIntent, ?array $budgetIntent, bool $moreProductsIntent = false, bool $listAllProductsIntent = false): array
    {
        $normalized = Str::lower(Str::ascii($message));
        
        // Detect khi user yêu cầu link sản phẩm
        $linkSignals = [
            'gui link', 'gửi link', 'link san pham', 'link sản phẩm', 'cho link', 'cho tôi link',
            'link cua san pham', 'link của sản phẩm', 'duong link', 'đường link', 'url',
            'send link', 'product link', 'give me link', 'share link'
        ];
        foreach ($linkSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return ['include' => true, 'mode' => 'single', 'request_link' => true];
            }
        }

        // Khi có budget intent, luôn hiển thị danh sách sản phẩm
        if ($priceIntent || $budgetIntent) {
            return ['include' => true, 'mode' => $budgetIntent ? 'list' : 'single'];
        }

        // Nếu user hỏi "còn sản phẩm nào khác" hoặc "có những sản phẩm gì", luôn hiển thị danh sách
        if ($moreProductsIntent || $listAllProductsIntent) {
            return ['include' => true, 'mode' => 'list'];
        }

        $listSignals = ['liet ke', 'danh sach', 'cho toi vai', 'goi y', 'de xuat', 'mot vai', 'several', 'list cho', 'cho minh vai san pham', 'nhieu san pham', 'nhiều sản phẩm'];
        foreach ($listSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return ['include' => true, 'mode' => 'list'];
            }
        }

        $singleSignals = ['san pham cu the', 'card san pham', 'nhan vao san pham', 'xem chi tiet', 'cho toi san pham', 'the hien card'];
        foreach ($singleSignals as $signal) {
            if (Str::contains($normalized, $signal)) {
                return ['include' => true, 'mode' => 'single'];
            }
        }

        // Mặc định: Khi có sản phẩm, luôn hiển thị card để user dễ thao tác
        // Trả về null để logic bên ngoài quyết định dựa trên số lượng listings
        return ['include' => true, 'mode' => 'auto'];
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

    /**
     * Lấy tất cả danh mục có sản phẩm (dùng khi user hỏi "có những sản phẩm gì")
     */
    private function getAllCategoriesWithProducts(): Collection
    {
        return Category::query()
            ->withCount(['listings' => fn ($q) => $q->where('status', 'approved')])
            ->having('listings_count', '>', 0)
            ->orderByDesc('listings_count')
            ->limit(10) // Lấy tối đa 10 danh mục để không quá dài
            ->get();
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
