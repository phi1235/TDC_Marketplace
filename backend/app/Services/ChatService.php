<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use App\Repositories\ChatRepository;
use App\Services\OpenAIService;
use App\Services\SupportContextService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function __construct(
        private ChatRepository $chatRepository,
        private OpenAIService $openAIService,
        private SupportContextService $supportContextService,
    ) {}

    public function startConversation(int $otherUserId, bool $isSupport = false): Conversation
    {
        $me = Auth::id();
        
        // Tìm conversation hiện có
        $existing = $this->chatRepository->findExistingConversation($me, $otherUserId, $isSupport);
        if ($existing) {
            return $existing;
        }

        // Tạo conversation mới trong transaction
        return DB::transaction(function () use ($me, $otherUserId, $isSupport) {
            $convo = $this->chatRepository->createConversation($isSupport, $isSupport);
            $this->chatRepository->addParticipant($convo->id, $me);
            $this->chatRepository->addParticipant($convo->id, $otherUserId);
            return $convo;
        });
    }

    public function sendMessage(Conversation $conversation, array $data): Message
    {
        try {
            Log::info('ChatService sendMessage called', [
                'conversation_id' => $conversation->id,
                'is_support' => $conversation->is_support,
                'type' => $data['type'] ?? 'text',
                'has_content' => !empty($data['content'] ?? null),
            ]);
        } catch (\Throwable $e) {}

        $senderId = Auth::id();
        $this->assertParticipant($conversation->id, $senderId);

        $message = $this->chatRepository->createMessage([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'type' => $data['type'] ?? 'text',
            'content' => $data['content'] ?? null,
            'meta' => $data['meta'] ?? null,
            'is_ai' => false,
        ]);

        $this->chatRepository->updateConversationLastMessageAt($conversation->id);

        // Broadcast user message event
        try {
            event(new MessageSent($message));
        } catch (\Throwable $e) {
            Log::warning('Failed to broadcast MessageSent event', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }

        // If this is a support conversation and message is text, generate AI response
        if ($message->sender_id !== null) {
            $handled = $this->handleAiToggleCommands($conversation, $message, $data);
            if ($handled) {
                return $message;
            }
        }

        if ($this->shouldTriggerAi($conversation, $data)) {
            try { Log::info('AI trigger - generating response', ['conversation_id' => $conversation->id, 'ai_enabled' => $conversation->ai_enabled]); } catch (\Throwable $e) {}
            $success = $this->generateAIResponse($conversation, $data['content']);

            if (!$success) {
                $this->sendAiFallbackMessage($conversation);
            }
        }

        return $message;
    }

    /**
     * Generate and send AI response for support conversation
     */
    private function generateAIResponse(Conversation $conversation, string $userMessage): bool
    {
        try {
            try { Log::info('AI generate - started', ['conversation_id' => $conversation->id]); } catch (\Throwable $e) {}
            // Get conversation history (last 10 messages, excluding the one just sent)
            $history = $this->chatRepository->getConversationMessages($conversation->id, 11);
            
            // Build conversation history for OpenAI (reverse to get chronological order)
            $conversationHistory = [];
            $messages = collect($history->items())->reverse()->values(); // Convert to collection, reverse to get oldest first
            
            foreach ($messages as $msg) {
                // Skip if content is empty
                if (empty($msg->content)) {
                    continue;
                }
                
                $conversationHistory[] = [
                    'role' => $msg->is_ai ? 'assistant' : 'user',
                    'content' => $msg->content,
                ];
            }

            // Generate AI response using internal listings context
            $contextPayload = $this->supportContextService->buildContext($userMessage);
            $contextSnippet = $contextPayload['context'] ?? null;
            $productCards = $contextPayload['products'] ?? [];

            $aiResponse = $this->openAIService->generateSupportResponse(
                $userMessage,
                $conversationHistory,
                $contextSnippet
            );
            $aiResponse = $this->sanitizeAiResponse($aiResponse);
            try { Log::info('AI generate - model response', ['has_response' => (bool)$aiResponse]); } catch (\Throwable $e) {}

            if ($aiResponse) {
                $meta = !empty($productCards) ? ['products' => $productCards] : null;

                // Create AI message
                $aiMessage = $this->chatRepository->createMessage([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null, // AI messages have no sender
                    'type' => 'text',
                    'content' => $aiResponse,
                    'meta' => $meta,
                    'is_ai' => true,
                ]);

                $this->chatRepository->updateConversationLastMessageAt($conversation->id);

                // Broadcast AI message
                try {
                    event(new MessageSent($aiMessage));
                } catch (\Throwable $e) {
                    Log::warning('Failed to broadcast AI MessageSent event', [
                        'message_id' => $aiMessage->id,
                        'error' => $e->getMessage(),
                    ]);
                }
                return true;
            } else {
                try { Log::warning('AI generate - empty response'); } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {
            Log::error('Failed to generate AI response', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return false;
    }

    private function sendAiFallbackMessage(Conversation $conversation): void
    {
        $fallbackMessage = "Xin lỗi, hệ thống đang quá tải. Vui lòng thử lại sau vài giây.";

        $aiMessage = $this->chatRepository->createMessage([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'type' => 'text',
            'content' => $fallbackMessage,
            'meta' => null,
            'is_ai' => true,
        ]);

        $this->chatRepository->updateConversationLastMessageAt($conversation->id);

        try {
            event(new MessageSent($aiMessage));
        } catch (\Throwable $e) {
            Log::warning('Failed to broadcast AI fallback message', [
                'message_id' => $aiMessage->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function listMessages(Conversation $conversation, int $perPage = 20): LengthAwarePaginator
    {
        $userId = Auth::id();
        $this->assertParticipant($conversation->id, $userId);
        
        return $this->chatRepository->getConversationMessages($conversation->id, $perPage);
    }

    public function getUserConversations(): Collection
    {
        $userId = Auth::id();
        return $this->chatRepository->getUserConversationsWithUnread($userId);
    }

    public function markConversationAsRead(int $conversationId): bool
    {
        $userId = Auth::id();
        $this->assertParticipant($conversationId, $userId);

        $lastMessage = $this->chatRepository->getLastMessageFromOthers($conversationId, $userId);
        
        if ($lastMessage) {
            return $this->chatRepository->updateLastReadMessage(
                $conversationId,
                $userId,
                $lastMessage->id
            );
        }

        return false;
    }

    protected function assertParticipant(int $conversationId, int $userId): void
    {
        $exists = $this->chatRepository->isParticipant($conversationId, $userId);
        abort_unless($exists, 403, 'Not participant of this conversation');
    }

    private function handleAiToggleCommands(Conversation $conversation, Message $message, array &$payload): bool
    {
        $content = trim((string) ($message->content ?? ''));
        if ($content === '') {
            return false;
        }

        $parsed = $this->parseAiCommand($content);
        if (!$parsed) {
            return false;
        }

        if ($parsed['action'] === 'enable') {
            $wasEnabled = (bool) $conversation->ai_enabled;
            if (!$wasEnabled) {
                $this->chatRepository->setConversationAiStatus($conversation->id, true);
                $conversation->ai_enabled = true;
            }

            $this->sendAiSystemMessage(
                $conversation->id,
                $wasEnabled
                    ? '🤖 AI hỗ trợ đã bật sẵn, bạn có thể tiếp tục đặt câu hỏi.'
                    : '🤖 AI hỗ trợ đã tham gia cuộc trò chuyện. Gõ /tathotro khi muốn quay lại chat với admin.'
            );

            if ($parsed['payload'] === '') {
                return true; // command only
            }

            $message->content = $parsed['payload'];
            $message->save();
            $payload['content'] = $parsed['payload'];
            return false;
        }

        if ($parsed['action'] === 'disable') {
            $wasEnabled = (bool) $conversation->ai_enabled;
            if ($wasEnabled) {
                $this->chatRepository->setConversationAiStatus($conversation->id, false);
                $conversation->ai_enabled = false;
            }

            $this->sendAiSystemMessage(
                $conversation->id,
                $wasEnabled
                    ? '✅ Đã tắt AI hỗ trợ. Bạn sẽ tiếp tục trao đổi trực tiếp với admin.'
                    : 'ℹ️ AI hỗ trợ đang ở trạng thái tắt. Không có thay đổi nào.'
            );

            return true;
        }

        return false;
    }

    private function parseAiCommand(string $content): ?array
    {
        $trimmed = trim($content);

        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^(\/hotro|@hotro|hotro)\b/iu', $trimmed, $matches)) {
            $payload = trim(mb_substr($trimmed, mb_strlen($matches[0])));
            return ['action' => 'enable', 'payload' => $payload];
        }

        if (preg_match('/^(\/tathotro|\/tat|@tathotro|@tat|tathotro)\b/iu', $trimmed, $matches)) {
            $payload = trim(mb_substr($trimmed, mb_strlen($matches[0])));
            return ['action' => 'disable', 'payload' => $payload];
        }

        return null;
    }

    private function sendAiSystemMessage(int $conversationId, string $content): void
    {
        $systemMessage = $this->chatRepository->createMessage([
            'conversation_id' => $conversationId,
            'sender_id' => null,
            'type' => 'text',
            'content' => $content,
            'meta' => null,
            'is_ai' => true,
        ]);

        $this->chatRepository->updateConversationLastMessageAt($conversationId);

        try {
            event(new MessageSent($systemMessage));
        } catch (\Throwable $e) {
            Log::warning('Failed to broadcast AI system message', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function shouldTriggerAi(Conversation $conversation, array $payload): bool
    {
        if (($payload['type'] ?? 'text') !== 'text') {
            return false;
        }

        $content = trim((string) ($payload['content'] ?? ''));
        if ($content === '') {
            return false;
        }

        return (bool) $conversation->ai_enabled;
    }

    private function sanitizeAiResponse(?string $response): ?string
    {
        if ($response === null) {
            return null;
        }

        return str_replace('**', '"', $response);
    }
}
