<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private int $timeout = 120; // Increased timeout for slow connections

    // Runtime configuration
    private string $apiUrl;  // Full URL endpoint
    private string $model;   // Model name
    private ?string $apiKey; // For OpenAI-compatible providers

    public function __construct()
    {
        // Get OpenAI configuration from environment variables
        // Prefer environment variables to avoid stale config cache
        $baseUrl = (string) (
            getenv('OPENAI_BASE_URL')
            ?: getenv('OPENAI_API_BASE')
            ?: ($_ENV['OPENAI_BASE_URL'] ?? ($_ENV['OPENAI_API_BASE'] ?? (config('services.openai.base_url') ?? '')))
        );
        $apiKey  = (string) (getenv('OPENAI_API_KEY') ?: ($_ENV['OPENAI_API_KEY'] ?? (config('services.openai.api_key') ?? '')));
        $model   = (string) (getenv('OPENAI_MODEL') ?: ($_ENV['OPENAI_MODEL'] ?? (config('services.openai.model') ?? 'gpt-4o-mini')));

        if (empty($baseUrl)) {
            throw new \RuntimeException('OPENAI_BASE_URL is required but not configured');
        }

            // Ensure no trailing slash handling
            $baseUrl = rtrim($baseUrl, '/');
            $this->apiUrl = $baseUrl . '/v1/chat/completions';
            $this->apiKey = $apiKey ?: null;
            $this->model  = $model;
    }

    /**
     * Generate AI response for support chat
     *
     * @param string $userMessage
     * @param array $conversationHistory Array of messages with 'role' and 'content'
     * @return string|null
     */
    public function generateSupportResponse(string $userMessage, array $conversationHistory = [], ?string $knowledgeContext = null): ?string
    {
        try {
            $messages = $this->buildMessages($userMessage, $conversationHistory, $knowledgeContext);

                $headers = ['Content-Type' => 'application/json'];
                if (!empty($this->apiKey)) {
                    $headers['Authorization'] = 'Bearer ' . $this->apiKey;
                }
            
            $response = Http::timeout($this->timeout)
                    ->withHeaders($headers)
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;
                    if ($content) {
                        try {
                            Log::info('OpenAI-compatible response generated successfully', [
                                'model' => $this->model,
                                'user_message_length' => strlen($userMessage),
                                'response_length' => strlen($content),
                            ]);
                        } catch (\Exception $logError) {}
                        return trim($content);
                    }
                }

            // Handle OpenAI errors
            $statusCode = $response->status();
            $errorBody = $response->body();

            try {
                Log::error('OpenAI-compatible API error', [
                    'status' => $statusCode,
                    'error' => $errorBody,
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }

            if ($statusCode === 429) {
                return "Xin lỗi, hệ thống đang quá tải. Vui lòng thử lại sau vài giây.";
            }

            return null;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            try {
                Log::error('OpenAI-compatible API connection timeout', [
                    'error' => $e->getMessage(),
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return "Xin lỗi, tôi đang gặp sự cố kết nối với AI. Vui lòng thử lại sau hoặc liên hệ admin.";
        } catch (\Exception $e) {
            try {
                Log::error('OpenAI-compatible API exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            } catch (\Exception $logError) {
                // Ignore logging errors
            }
            return "Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau hoặc liên hệ admin.";
        }
    }

    /**
     * Build messages array for chat API
     *
     * @param string $userMessage
     * @param array $conversationHistory
     * @return array
     */
    private function buildMessages(string $userMessage, array $conversationHistory, ?string $knowledgeContext): array
    {
        $systemPrompt = $this->getSystemPrompt();

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
        ];

        if (!empty($knowledgeContext)) {
            $messages[] = [
                'role' => 'system',
                'content' => "=== THÔNG TIN SẢN PHẨM/DANH MỤC NỘI BỘ (DỮ LIỆU THẬT TỪ DATABASE) ===\n" . 
                            $knowledgeContext . 
                            "\n\n=== CẢNH BÁO NGHIÊM NGẶT ===" .
                            "\n1. CHỈ được sử dụng thông tin sản phẩm có trong danh sách trên." .
                            "\n2. TUYỆT ĐỐI KHÔNG được tạo, sáng tạo, hoặc 'chế' bất kỳ thông tin sản phẩm nào không có trong danh sách trên." .
                            "\n3. TUYỆT ĐỐI KHÔNG được tạo tên người bán, tên sản phẩm, giá cả, hoặc bất kỳ thông tin nào không có trong danh sách trên." .
                            "\n4. Nếu danh sách trên KHÔNG có thông tin 'Người bán', TUYỆT ĐỐI KHÔNG được tạo tên người bán như 'Nguyễn Văn A', 'Trần Thị B', v.v." .
                            "\n5. Nếu danh sách trên KHÔNG có sản phẩm nào, PHẢI nói rõ 'Hiện chưa có sản phẩm phù hợp trong kho' - KHÔNG được tạo ví dụ." .
                            "\n6. CHỈ được liệt kê CHÍNH XÁC các sản phẩm có trong danh sách trên với thông tin đúng như trong danh sách.",
            ];
        } else {
            // Nếu không có context, thêm message rõ ràng
            $messages[] = [
                'role' => 'system',
                'content' => "=== CẢNH BÁO NGHIÊM NGẶT ===" .
                            "\nHiện KHÔNG có dữ liệu sản phẩm nào trong kho phù hợp với yêu cầu." .
                            "\nBạn PHẢI trả lời: 'Hiện chưa có sản phẩm phù hợp trong kho. Bạn có thể thử tìm kiếm với từ khóa khác hoặc mở rộng tiêu chí tìm kiếm.'" .
                            "\nTUYỆT ĐỐI KHÔNG được:" .
                            "\n- Tạo hoặc liệt kê sản phẩm giả" .
                            "\n- Tạo tên sản phẩm như 'Sách giáo khoa Toán 12', 'Sách giáo khoa Ngữ văn 11', v.v." .
                            "\n- Tạo tên người bán như 'Nguyễn Văn A', 'Trần Thị B', v.v." .
                            "\n- Tạo giá cả hoặc bất kỳ thông tin sản phẩm nào" .
                            "\n- Đưa ra ví dụ sản phẩm" .
                            "\nCHỈ được nói rõ là không có sản phẩm phù hợp.",
            ];
        }

        // Add conversation history (last 10 messages to stay within token limit)
        $history = array_slice($conversationHistory, -10);
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    /**
     * Get system prompt for TDC Marketplace support bot
     *
     * @return string
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Bạn là trợ lý AI cho TDC Marketplace (nền tảng trao đổi đồ học tập cũ của sinh viên Trường Cao đẳng Công nghệ Thủ Đức).

QUY TẮC NGHIÊM NGẶT - TUYỆT ĐỐI TUÂN THỦ:

VỀ TƯƠNG TÁC THÔNG THƯỜNG:
- Khi người dùng chỉ chào hỏi (alo, hello, chào bạn...) hoặc tương tác thông thường, hãy trả lời thân thiện, tự nhiên như một người hỗ trợ thật.
- KHÔNG cần tìm kiếm sản phẩm khi người dùng chỉ chào hỏi hoặc nói chuyện thông thường.
- Chỉ khi người dùng hỏi về sản phẩm, giá cả, tìm kiếm, mua bán... mới cần sử dụng dữ liệu từ database.

VỀ HỎI "CÓ NHỮNG SẢN PHẨM GÌ":
- Khi người dùng hỏi "có những sản phẩm gì", "trang web có gì", "danh sách sản phẩm" (câu hỏi chung chung về sản phẩm):
  + Bạn PHẢI hỏi lại người dùng: "Bạn đang quan tâm đến danh mục nào?"
  + Liệt kê các danh mục có trong context để người dùng lựa chọn
  + Đợi người dùng chọn danh mục trước khi giới thiệu sản phẩm
  + KHÔNG được tự động liệt kê tất cả sản phẩm ngay lập tức
- Sau khi người dùng chọn danh mục (ví dụ: "laptop", "sách", "máy tính"), bạn mới giới thiệu một vài sản phẩm của danh mục đó.

VỀ DỮ LIỆU SẢN PHẨM - QUY TẮC CỰC KỲ NGHIÊM NGẶT:
1. CHỈ trả lời về sản phẩm dựa trên dữ liệu được cung cấp trong "Thông tin sản phẩm/danh mục nội bộ". 
2. TUYỆT ĐỐI KHÔNG được tạo, sáng tạo, hoặc "chế" bất kỳ thông tin sản phẩm nào không có trong context.
3. TUYỆT ĐỐI KHÔNG được liệt kê sản phẩm, giá cả, tên người bán, hoặc bất kỳ thông tin nào không có trong context được cung cấp.
4. TUYỆT ĐỐI KHÔNG được tạo ví dụ sản phẩm như "Sách giáo khoa Toán 12", "Nguyễn Văn A", "Trần Thị B" nếu không có trong context.
5. Nếu context KHÔNG có sản phẩm phù hợp, PHẢI nói rõ: "Hiện chưa có sản phẩm phù hợp trong kho" hoặc "Hiện chưa có dữ liệu phù hợp" - TUYỆT ĐỐI KHÔNG được tự tạo ví dụ hoặc liệt kê sản phẩm giả.
6. Nếu context có sản phẩm, PHẢI liệt kê CHÍNH XÁC các sản phẩm đó với thông tin đầy đủ (tên, giá, danh mục, tình trạng) như trong context - KHÔNG được thêm thông tin người bán nếu không có trong context.
7. Nếu context không có thông tin "Người bán", TUYỆT ĐỐI KHÔNG được tạo tên người bán như "Nguyễn Văn A", "Trần Thị B", v.v.
8. Nếu context chỉ có danh sách sản phẩm với tên và giá, CHỈ được liệt kê những thông tin đó - KHÔNG được thêm thông tin không có trong context.

Phong cách phản hồi:
- Khi người dùng chào hỏi: Trả lời thân thiện, tự nhiên, ngắn gọn. Ví dụ: "Chào bạn! Mình có thể giúp gì cho bạn hôm nay?" hoặc "Xin chào! Bạn cần tìm gì trên TDC Marketplace không?"
- Khi người dùng hỏi về sản phẩm: Trả lời đúng trọng tâm, tối đa vài câu; ưu tiên đưa thẳng thông tin người dùng cần (tên sản phẩm, giá, tình trạng, người bán...).
- Khi có sản phẩm trong context: PHẢI liệt kê các sản phẩm đó ngay lập tức, không chỉ nói số lượng. Ví dụ: "Dưới đây là các sản phẩm: [liệt kê sản phẩm từ context]"
- Khi người dùng hỏi "cho tôi xem", "xem sản phẩm", "liệt kê": PHẢI liệt kê tất cả sản phẩm có trong context, không chỉ nói số lượng.
- Khi gợi ý sản phẩm: Hệ thống sẽ tự động hiển thị card sản phẩm để người dùng dễ nhấn vào xem chi tiết. Bạn chỉ cần giới thiệu sản phẩm một cách tự nhiên.
- Khi người dùng yêu cầu link sản phẩm (ví dụ: "gửi link", "cho tôi link", "link sản phẩm"): 
  + Hệ thống sẽ TỰ ĐỘNG hiển thị card sản phẩm bên dưới tin nhắn của bạn. Card này có thể nhấn vào để xem trang chi tiết.
  + Bạn KHÔNG cần gửi text link như "/listings/1" trong tin nhắn.
  + Chỉ cần nói: "Đây là sản phẩm bạn đang tìm. Bạn có thể nhấn vào card sản phẩm bên dưới để xem chi tiết nhé!" hoặc tương tự.
  + TUYỆT ĐỐI KHÔNG được gửi text link thô như "/listings/1" hoặc "/listings/123" trong tin nhắn.
- Nếu context có mục "Sản phẩm giá thấp nhất/giá cao nhất…", danh sách sản phẩm hoặc đề xuất theo ngân sách, hãy nêu CHÍNH XÁC các sản phẩm đó (và giá) theo thứ tự phù hợp; TUYỆT ĐỐI KHÔNG sáng tạo thêm dữ liệu ngoài context.
- Khi người dùng hỏi theo khoảng giá (trên/dưới/khoảng X triệu), hãy trả lời bằng các sản phẩm đúng khoảng giá nếu context cung cấp; nếu KHÔNG có trong context, PHẢI nói rõ "Hiện chưa có sản phẩm phù hợp với khoảng giá này trong kho".
- Nếu context không chứa dữ liệu phù hợp, PHẢI nói rõ "Hiện chưa có dữ liệu phù hợp trong kho" và gợi ý người dùng mô tả cụ thể hơn; TUYỆT ĐỐI KHÔNG dựng ví dụ hoặc tạo thông tin giả.
- Chỉ hướng dẫn từng bước khi người dùng chủ động hỏi "làm sao", "hướng dẫn".
- Diễn đạt tự nhiên, không bao câu bằng ký tự ".
- Nếu cần làm rõ, chỉ hỏi lại một câu; khi nhắc lưu ý an toàn, gói gọn trong một câu cuối.

Thông tin nền cần nhớ:
- Nền tảng kết nối buyer (tân sinh viên) và seller (sinh viên khóa trên/cựu sinh viên) để mua bán sách, laptop, dụng cụ học tập.
- Các tính năng chính: đăng/duyệt tin, tìm kiếm nâng cao, chat thương lượng, tạo offer, wishlist, đánh giá, báo cáo vi phạm, thông báo realtime.
- Quy trình chuẩn: tìm sản phẩm → chat thương lượng → tạo offer/đơn → hẹn giao nhận tại campus, ưu tiên giao dịch an toàn.

NHẮC LẠI: CHỈ sử dụng dữ liệu từ context được cung cấp. KHÔNG được tạo, sáng tạo, hoặc "chế" bất kỳ thông tin nào không có trong context.
PROMPT;
    }
}
