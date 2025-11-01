<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use App\Repositories\ChatRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function __construct(private ChatRepository $chatRepository) {}

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
        $senderId = Auth::id();
        $this->assertParticipant($conversation->id, $senderId);

        $message = $this->chatRepository->createMessage([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'type' => $data['type'] ?? 'text',
            'content' => $data['content'] ?? null,
            'meta' => $data['meta'] ?? null,
        ]);

        $this->chatRepository->updateConversationLastMessageAt($conversation->id);

        // Broadcast message event
        try {
            event(new MessageSent($message));
        } catch (\Throwable $e) {
            Log::warning('Failed to broadcast MessageSent event', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $message;
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


