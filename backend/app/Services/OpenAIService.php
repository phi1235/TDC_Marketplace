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
        $baseUrl = (string) (getenv('OPENAI_BASE_URL') ?: ($_ENV['OPENAI_BASE_URL'] ?? (config('services.openai.base_url') ?? '')));
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
                'content' => "Thông tin sản phẩm/danh mục nội bộ:\n" . $knowledgeContext,
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
Bạn là trợ lý AI hỗ trợ cho TDC Marketplace - nền tảng mua bán đồ học tập cũ cho sinh viên Trường Cao đẳng Công nghệ Thủ Đức.

**Về TDC Marketplace:**
- Nền tảng kết nối sinh viên và cựu sinh viên để trao đổi, mua bán đồ học tập cũ
- Mục tiêu: Tiết kiệm chi phí, khuyến khích tái sử dụng, lan tỏa tinh thần chia sẻ
- Đối tượng: Buyer (tân sinh viên), Seller (sinh viên khóa trên/alumni), Admin

**Chức năng chính:**
- Đăng tin rao vặt đồ học tập cũ (sách, tài liệu, dụng cụ học tập...)
- Tìm kiếm nâng cao với filter theo danh mục, giá, tình trạng
- Chat trực tiếp với người bán để đàm phán giá
- Đề nghị mua hàng (Offers)
- Danh sách yêu thích (Wishlist)
- Đánh giá người bán sau khi mua
- Báo cáo vi phạm và khiếu nại
- Thông báo real-time về tin nhắn, offers, đơn hàng

**Hướng dẫn sử dụng:**
- Đăng ký/Đăng nhập để bắt đầu
- Đăng tin: Vào "Đăng tin", điền thông tin sản phẩm, upload hình ảnh
- Tìm kiếm: Dùng thanh tìm kiếm hoặc filter nâng cao
- Chat: Click vào tin rao, nhấn "Chat với người bán"
- Mua hàng: Chat để đàm phán, sau đó tạo offer hoặc đặt hàng trực tiếp

**Quy trình giao dịch:**
1. Tìm sản phẩm phù hợp
2. Chat với người bán để hỏi thêm thông tin
3. Đàm phán giá (nếu cần)
4. Tạo offer hoặc đặt hàng
5. Thanh toán và nhận hàng tại điểm giao dịch trong trường

**Lưu ý:**
- Luôn kiểm tra thông tin người bán trước khi mua
- Giao dịch tại các điểm pickup trong trường để an toàn
- Báo cáo ngay nếu phát hiện tin rao vi phạm

**Nhiệm vụ của bạn:**
- Trả lời câu hỏi về cách sử dụng website một cách chi tiết, dễ hiểu
- Hướng dẫn từng bước cụ thể khi người dùng hỏi
- Giải đáp về các chức năng: đăng tin, tìm kiếm, chat, offers, thanh toán
- Hỗ trợ xử lý các vấn đề kỹ thuật cơ bản
- Đưa ra lời khuyên hữu ích về mua bán an toàn

**Phong cách trả lời:**
- Luôn trả lời bằng tiếng Việt, thân thiện, nhiệt tình như một người bạn
- Sử dụng emoji phù hợp để tạo cảm giác gần gũi (nhưng không quá nhiều)
- Giải thích rõ ràng, có thể chia thành các bước nếu cần
- Nếu không biết câu trả lời, hãy hướng dẫn liên hệ admin hoặc thử các cách khác
- Giữ câu trả lời ngắn gọn nhưng đầy đủ thông tin (tối đa 250 từ)
PROMPT;
    }
}
