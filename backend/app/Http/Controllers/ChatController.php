<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatService;
use App\Http\Requests\StartConversationRequest;
use App\Http\Requests\SendMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function start(StartConversationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $convo = $this->chatService->startConversation(
            (int) $data['user_id'],
            (bool) ($data['is_support'] ?? false)
        );
        
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
        $convos = $this->chatService->getUserConversations();
        
        return response()->json($convos);
    }

    public function startSupport(): JsonResponse
    {
        // Tìm admin bất kỳ (ưu tiên đầu tiên)
        $admin = User::role('admin')->first();
        abort_unless($admin, 404, 'No admin available');
        
        $convo = $this->chatService->startConversation($admin->id, true);
        
        return response()->json($convo->load('participants.user:id,name'));
    }

    public function send(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $data = $request->validated();
        $meta = $data['meta'] ?? [];
        $type = $data['type'] ?? 'text';

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

    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $this->chatService->markConversationAsRead($conversation->id);
        
        return response()->json(['success' => true]);
    }
}


