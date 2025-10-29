<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    public function startConversation(int $otherUserId, bool $isSupport = false): Conversation
    {
        $me = Auth::id();
        // Tìm convo 2 người (me, other) chưa phải support
        $existing = Conversation::where('is_support', $isSupport)
            ->whereHas('participants', fn($q)=>$q->where('user_id', $me))
            ->whereHas('participants', fn($q)=>$q->where('user_id', $otherUserId))
            ->first();
        if ($existing) return $existing;

        return DB::transaction(function () use ($me, $otherUserId, $isSupport) {
            $convo = Conversation::create(['is_support' => $isSupport]);
            ConversationParticipant::create(['conversation_id'=>$convo->id,'user_id'=>$me]);
            ConversationParticipant::create(['conversation_id'=>$convo->id,'user_id'=>$otherUserId]);
            return $convo;
        });
    }

    public function sendMessage(Conversation $conversation, array $data): Message
    {
        $senderId = Auth::id();
        $this->assertParticipant($conversation->id, $senderId);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'type' => $data['type'] ?? 'text',
            'content' => $data['content'] ?? null,
            'meta' => $data['meta'] ?? null,
        ]);
        $conversation->update(['last_message_at' => now()]);
        try { event(new MessageSent($message)); } catch (\Throwable $e) {}
        return $message;
    }

    public function listMessages(Conversation $conversation, int $perPage = 20)
    {
        $this->assertParticipant($conversation->id, Auth::id());
        return $conversation->messages()->with('sender:id,name')->orderByDesc('id')->paginate($perPage);
    }

    protected function assertParticipant(int $conversationId, int $userId): void
    {
        $exists = ConversationParticipant::where('conversation_id',$conversationId)->where('user_id',$userId)->exists();
        abort_unless($exists, 403, 'Not participant of this conversation');
    }
}


