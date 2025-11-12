<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChatRepository
{
    protected Conversation $conversationModel;
    protected ConversationParticipant $participantModel;
    protected Message $messageModel;

    public function __construct(
        Conversation $conversationModel,
        ConversationParticipant $participantModel,
        Message $messageModel
    ) {
        $this->conversationModel = $conversationModel;
        $this->participantModel = $participantModel;
        $this->messageModel = $messageModel;
    }

    /**
     * Tìm conversation hiện có giữa 2 users
     */
    public function findExistingConversation(int $userId1, int $userId2, bool $isSupport = false): ?Conversation
    {
        return $this->conversationModel
            ->where('is_support', $isSupport)
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId1))
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId2))
            ->first();
    }

    /**
     * Tạo conversation mới
     */
    public function createConversation(bool $isSupport = false): Conversation
    {
        return $this->conversationModel->create(['is_support' => $isSupport]);
    }

    /**
     * Thêm participant vào conversation
     */
    public function addParticipant(int $conversationId, int $userId): ConversationParticipant
    {
        return $this->participantModel->create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Kiểm tra user có phải participant không
     */
    public function isParticipant(int $conversationId, int $userId): bool
    {
        return $this->participantModel
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Lấy danh sách conversations của user với unread count
     */
    public function getUserConversationsWithUnread(int $userId): Collection
    {
        $conversations = $this->conversationModel
            ->whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->with(['participants.user:id,name', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        foreach ($conversations as $convo) {
            $participant = $this->participantModel
                ->where('conversation_id', $convo->id)
                ->where('user_id', $userId)
                ->first();

            $lastReadId = $participant->last_read_message_id ?? 0;
            $unreadCount = $this->messageModel
                ->where('conversation_id', $convo->id)
                ->where(function($q) use ($userId) {
                    $q->where('sender_id', '!=', $userId)
                      ->orWhereNull('sender_id'); // Include AI messages
                })
                ->where('id', '>', $lastReadId)
                ->count();

            $convo->unread_count = $unreadCount;
            $convo->last_message = $convo->messages->first();
        }

        return $conversations;
    }

    /**
     * Lấy messages của conversation với pagination
     */
    public function getConversationMessages(int $conversationId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->messageModel
            ->where('conversation_id', $conversationId)
            ->with('sender:id,name')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Lấy last message của conversation từ người khác (không phải user hiện tại)
     */
    public function getLastMessageFromOthers(int $conversationId, int $userId): ?Message
    {
        return $this->messageModel
            ->where('conversation_id', $conversationId)
            ->where(function($q) use ($userId) {
                $q->where('sender_id', '!=', $userId)
                  ->orWhereNull('sender_id'); // Include AI messages
            })
            ->latest('id')
            ->first();
    }

    /**
     * Cập nhật last_read_message_id cho participant
     */
    public function updateLastReadMessage(int $conversationId, int $userId, int $messageId): bool
    {
        return $this->participantModel
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['last_read_message_id' => $messageId]);
    }

    /**
     * Tạo message mới
     */
    public function createMessage(array $data): Message
    {
        return $this->messageModel->create($data);
    }

    /**
     * Cập nhật last_message_at của conversation
     */
    public function updateConversationLastMessageAt(int $conversationId): bool
    {
        return $this->conversationModel
            ->where('id', $conversationId)
            ->update(['last_message_at' => now()]);
    }

    /**
     * Lấy conversation theo ID
     */
    public function findConversation(int $conversationId): ?Conversation
    {
        return $this->conversationModel->find($conversationId);
    }

    /**
     * Lấy conversation với participants
     */
    public function findConversationWithParticipants(int $conversationId): ?Conversation
    {
        return $this->conversationModel
            ->with('participants.user:id,name')
            ->find($conversationId);
    }
}

