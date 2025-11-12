<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use App\Repositories\ChatRepository;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function __construct(
        private ChatRepository $chatRepository,
        private OpenAIService $openAIService
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
            $convo = $this->chatRepository->createConversation($isSupport);
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
        if ($conversation->is_support && ($data['type'] ?? 'text') === 'text' && !empty($data['content'])) {
            try { Log::info('AI trigger - generating response', ['conversation_id' => $conversation->id]); } catch (\Throwable $e) {}
            $this->generateAIResponse($conversation, $data['content']);
        }

        return $message;
    }

    /**
     * Generate and send AI response for support conversation
     */
    private function generateAIResponse(Conversation $conversation, string $userMessage): void
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

            // Generate AI response
            $aiResponse = $this->openAIService->generateSupportResponse($userMessage, $conversationHistory);
            try { Log::info('AI generate - model response', ['has_response' => (bool)$aiResponse]); } catch (\Throwable $e) {}

            if ($aiResponse) {
                // Create AI message
                $aiMessage = $this->chatRepository->createMessage([
                    'conversation_id' => $conversation->id,
                    'sender_id' => null, // AI messages have no sender
                    'type' => 'text',
                    'content' => $aiResponse,
                    'meta' => null,
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
}


