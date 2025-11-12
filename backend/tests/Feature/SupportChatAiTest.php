<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Services\OpenAIService;
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

            public function __construct()
            {
                // Skip parent construction because we do not need real HTTP config here.
            }

            public function generateSupportResponse(string $userMessage, array $conversationHistory = []): ?string
            {
                $this->receivedMessage = $userMessage;
                $this->receivedHistory = $conversationHistory;

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
}
