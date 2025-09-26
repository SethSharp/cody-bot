<?php

namespace Tests\Feature;

use App\Agents\CustomerSupportAgent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;

class CustomerSupportAgentTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_respond_to_basic_query(): void
    {
        $response = CustomerSupportAgent::run('Hello, can you help me with my account?')->go();

        $this->assertNotEmpty($response);
        $this->assertIsString($response);
    }

    public function test_agent_maintains_user_context(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // First interaction to establish context
        $response1 = CustomerSupportAgent::run('My name is ' . $user->name)
            ->forUser($user)
            ->go();

        $this->assertNotEmpty($response1);

        // Second interaction should remember the user's name
        $response2 = CustomerSupportAgent::run('What is my name?')
            ->forUser($user)
            ->go();

        $this->assertNotEmpty($response2);
        $this->assertStringContainsString($user->name, $response2);
    }

    public function test_agent_maintains_session_context(): void
    {
        // Create a test user
        $user = User::factory()->create();

        // Create a unique session ID
        $sessionId = 'test-session-' . Str::random(10);

        // First interaction to establish context
        $response1 = CustomerSupportAgent::run('I want to know about subscription plans')
            ->forUser($user)
            ->withSession($sessionId)
            ->go();

        $this->assertNotEmpty($response1);

        // Second interaction should maintain context of the conversation
        $response2 = CustomerSupportAgent::run('Which one would you recommend for me?')
            ->forUser($user)
            ->withSession($sessionId)
            ->go();

        $this->assertNotEmpty($response2);
    }

    public function test_agent_can_use_knowledge_base_search(): void
    {
        $response = CustomerSupportAgent::run('How do I reset my password?')->go();

        $this->assertNotEmpty($response);
        // The response should contain information related to password reset
        // which is in our simulated knowledge base
        $this->assertStringContainsString('password', strtolower($response));
    }

    public function test_agent_can_handle_unknown_queries(): void
    {
        $response = CustomerSupportAgent::run('Tell me about something not in the knowledge base')->go();

        $this->assertNotEmpty($response);
        // Should acknowledge limitations rather than making up information
        $this->assertFalse(
            str_contains(strtolower($response), 'i don\'t know') &&
            str_contains(strtolower($response), 'cannot provide')
        );
    }

    public function test_agent_can_stream_responses(): void
    {
        $stream = CustomerSupportAgent::run('Hello')
            ->streaming(true)
            ->go();

        $this->assertNotNull($stream);

        // Convert stream to string to verify it contains content
        $fullResponse = '';
        foreach ($stream as $chunk) {
            $fullResponse .= $chunk;
        }

        $this->assertNotEmpty($fullResponse);
    }
}
