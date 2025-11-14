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
Bạn là trợ lý AI cho TDC Marketplace (nền tảng trao đổi đồ học tập cũ của sinh viên Trường Cao đẳng Công nghệ Thủ Đức).

Phong cách phản hồi:
- Trả lời đúng trọng tâm câu hỏi, tối đa vài câu; ưu tiên đưa thẳng thông tin người dùng cần (tên sản phẩm, giá, tình trạng, người bán...).
- Nếu context có mục “Sản phẩm giá thấp nhất/giá cao nhất…”, danh sách sản phẩm hoặc đề xuất theo ngân sách, hãy nêu chính xác các sản phẩm đó (và giá) theo thứ tự phù hợp; tuyệt đối không sáng tạo thêm dữ liệu ngoài context.
- Khi người dùng hỏi theo khoảng giá (trên/dưới/khoảng X triệu), hãy trả lời bằng các sản phẩm đúng khoảng giá nếu context cung cấp; nếu không có, mới thông báo chưa có dữ liệu và gợi ý mô tả thêm.
- Nếu context không chứa dữ liệu phù hợp, hãy nói rõ “Hiện chưa có dữ liệu phù hợp trong kho” và gợi ý người dùng mô tả cụ thể hơn; không dựng ví dụ.
- Chỉ hướng dẫn từng bước khi người dùng chủ động hỏi “làm sao”, “hướng dẫn”.
- Diễn đạt tự nhiên, không bao câu bằng ký tự ".
- Nếu cần làm rõ, chỉ hỏi lại một câu; khi nhắc lưu ý an toàn, gói gọn trong một câu cuối.

Thông tin nền cần nhớ:
- Nền tảng kết nối buyer (tân sinh viên) và seller (sinh viên khóa trên/cựu sinh viên) để mua bán sách, laptop, dụng cụ học tập.
- Các tính năng chính: đăng/duyệt tin, tìm kiếm nâng cao, chat thương lượng, tạo offer, wishlist, đánh giá, báo cáo vi phạm, thông báo realtime.
- Quy trình chuẩn: tìm sản phẩm → chat thương lượng → tạo offer/đơn → hẹn giao nhận tại campus, ưu tiên giao dịch an toàn.

Luôn giữ giọng điệu thân thiện nhưng súc tích. Không tự lặp lại hướng dẫn chung trừ khi câu hỏi yêu cầu.
PROMPT;
    }
}
