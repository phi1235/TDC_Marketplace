<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Services\OpenAIService;
use App\Services\ChatService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupportChatAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_conversation_generates_ai_message(): void
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $conversation = Conversation::create(['is_support' => true]);
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $admin->id,
        ]);

        $fakeService = new class extends OpenAIService {
            public string $receivedMessage = '';
            public array $receivedHistory = [];
            public ?string $receivedContext = null;

            public function __construct()
            {
                // Skip parent construction because we do not need real HTTP config here.
            }

            public function generateSupportResponse(string $userMessage, array $conversationHistory = [], ?string $knowledgeContext = null): ?string
            {
                $this->receivedMessage = $userMessage;
                $this->receivedHistory = $conversationHistory;
                $this->receivedContext = $knowledgeContext;

                return 'Xin chào, tôi là AI hỗ trợ 🎓';
            }
        };

        $this->app->instance(OpenAIService::class, $fakeService);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/chat/conversations/{$conversation->id}/messages", [
            'type' => 'text',
            'content' => 'Mình cần được hỗ trợ về cách đăng tin',
        ]);

        $response->assertOk();

        $this->assertSame(
            'Mình cần được hỗ trợ về cách đăng tin',
            $fakeService->receivedMessage,
            'OpenAIService stub should receive the latest user message.'
        );

        $this->assertNotNull(
            $fakeService->receivedContext,
            'AI context should be generated for support conversation.'
        );

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'is_ai' => true,
            'content' => 'Xin chào, tôi là AI hỗ trợ 🎓',
        ]);

        $this->assertEquals(
            2,
            Message::where('conversation_id', $conversation->id)->count(),
            'Conversation should contain both user and AI messages.'
        );
    }

    public function test_ai_can_be_toggled_inside_regular_conversation(): void
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $fakeService = new class extends OpenAIService {
            public array $messages = [];
            public array $contexts = [];

            public function __construct() {}

            public function generateSupportResponse(string $userMessage, array $conversationHistory = [], ?string $knowledgeContext = null): ?string
            {
                $this->messages[] = $userMessage;
                $this->contexts[] = $knowledgeContext;
                return 'Xin chào, tôi là AI hỗ trợ 🎓';
            }
        };

        $this->app->instance(OpenAIService::class, $fakeService);

        Sanctum::actingAs($user);

        /** @var ChatService $chatService */
        $chatService = $this->app->make(ChatService::class);
        $conversation = $chatService->startConversation($admin->id, false);

        // 1) Enable AI via command and include question in the same message
        $this->postJson("/api/chat/conversations/{$conversation->id}/messages", [
            'type' => 'text',
            'content' => '/hotro Xin chào AI?',
        ])->assertOk();

        $conversation->refresh();
        $this->assertTrue($conversation->ai_enabled, 'AI flag should be turned on after /hotro');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'is_ai' => true,
            'content' => '🤖 AI hỗ trợ đã tham gia cuộc trò chuyện. Gõ /tathotro khi muốn quay lại chat với admin.',
        ]);

        $this->assertSame(['Xin chào AI?'], $fakeService->messages, 'AI should answer the sanitized payload after /hotro command.');
        $this->assertNotEmpty($fakeService->contexts, 'Context snapshot should be passed when AI responds.');

        // 2) Disable AI
        $this->postJson("/api/chat/conversations/{$conversation->id}/messages", [
            'type' => 'text',
            'content' => '/tathotro',
        ])->assertOk();

        $conversation->refresh();
        $this->assertFalse($conversation->ai_enabled, 'AI flag should be off after /tathotro');

        // 3) Send another message, AI should not respond because it is disabled
        $this->postJson("/api/chat/conversations/{$conversation->id}/messages", [
            'type' => 'text',
            'content' => 'Có admin nào ở đó không?',
        ])->assertOk();

        $this->assertSame(1, count($fakeService->messages), 'AI should not respond once /tathotro was issued.');
    }
}
