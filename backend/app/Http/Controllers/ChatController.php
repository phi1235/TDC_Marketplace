<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'is_support' => 'sometimes|boolean',
        ]);
        $convo = $this->chatService->startConversation((int)$data['user_id'], (bool)($data['is_support'] ?? false));
        return response()->json($convo->load('participants.user:id,name'));
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $perPage = (int) ($request->input('per_page', 20));
        $messages = $this->chatService->listMessages($conversation, $perPage);
        return response()->json($messages);
    }

    public function myConversations(): JsonResponse
    {
        $userId = auth()->id();
        $convos = \App\Models\Conversation::whereHas('participants', fn($q)=>$q->where('user_id',$userId))
            ->with(['participants.user:id,name', 'messages' => function($q){ $q->latest()->limit(1); }])
            ->orderByDesc('last_message_at')
            ->paginate(20);
        return response()->json($convos);
    }

    public function startSupport(): JsonResponse
    {
        // Tìm admin bất kỳ (ưu tiên đầu tiên)
        $admin = \App\Models\User::role('admin')->first();
        abort_unless($admin, 404, 'No admin available');
        $convo = $this->chatService->startConversation($admin->id, true);
        return response()->json($convo->load('participants.user:id,name'));
    }

    public function send(Request $request, Conversation $conversation): JsonResponse
    {
        $data = $request->validate([
            'type' => 'sometimes|string|in:text,image',
            'content' => 'nullable|string',
            'meta' => 'nullable|array',
            'image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $type = $data['type'] ?? 'text';
        $meta = $data['meta'] ?? [];

        // Handle image upload
        if ($request->hasFile('image')) {
            $type = 'image';
            $file = $request->file('image');
            $path = $file->store('chat/images', 'public');
            $meta['image_url'] = Storage::url($path);
            $meta['image_name'] = $file->getClientOriginalName();
            $meta['image_size'] = $file->getSize();
        }

        $msg = $this->chatService->sendMessage($conversation, [
            'type' => $type,
            'content' => $data['content'] ?? null,
            'meta' => $meta,
        ]);
        return response()->json($msg->load('sender:id,name'));
    }
}


