<?php

namespace App\Examples;

use App\Agents\CustomerSupportAgent;
use Illuminate\Support\Facades\Auth;

class CustomerSupportAgentExample
{
    /**
     * Example of using the customer support agent
     *
     * @return string The agent's response
     */
    public function basicExample(string $customerQuery): string
    {
        // Simple usage - just ask a question
        $response = CustomerSupportAgent::run($customerQuery)->go();

        return $response;
    }

    /**
     * Example with user context for persistent memory
     *
     * @param string $customerQuery The customer's question
     * @return string The agent's response
     */
    public function withUserContext(string $customerQuery): string
    {
        // Get the current authenticated user (or you could pass any user model)
        $user = Auth::user();

        // Run the agent with user context to enable persistent memory
        $response = CustomerSupportAgent::run($customerQuery)
            ->forUser($user)
            ->go();

        return $response;
    }

    /**
     * Example with session persistence for maintaining conversation history
     *
     * @param string $customerQuery The customer's question
     * @param string $sessionId A unique session identifier for this conversation thread
     * @return string The agent's response
     */
    public function withSessionPersistence(string $customerQuery, string $sessionId): string
    {
        $user = Auth::user();

        // Run with both user context and session ID for complete context awareness
        $response = CustomerSupportAgent::run($customerQuery)
            ->forUser($user)
            ->withSession($sessionId)
            ->go();

        return $response;
    }

    /**
     * Example with additional context parameters
     *
     * @param string $customerQuery The customer's question
     * @param string $sessionId A unique session identifier
     * @param array $additionalContext Additional context for the agent
     * @return string The agent's response
     */
    public function withAdditionalContext(
        string $customerQuery,
        string $sessionId,
        array $additionalContext = []
    ): string {
        $user = Auth::user();

        // Pass additional parameters to the agent (will be available in the prompt template)
        $response = CustomerSupportAgent::run($customerQuery)
            ->forUser($user)
            ->withSession($sessionId)
            ->withParameters([
                'context' => $additionalContext['context'] ?? null,
                'priority' => $additionalContext['priority'] ?? 'normal',
                'metadata' => $additionalContext['metadata'] ?? [],
            ])
            ->go();

        return $response;
    }

    /**
     * Example with streaming responses for real-time UI updates
     *
     * @param string $customerQuery The customer's question
     * @param string $sessionId A unique session identifier
     * @return \Illuminate\Support\Enumerable A stream of response chunks
     */
    public function withStreamingResponse(string $customerQuery, string $sessionId)
    {
        $user = Auth::user();

        // Enable streaming for real-time response chunks
        $stream = CustomerSupportAgent::run($customerQuery)
            ->forUser($user)
            ->withSession($sessionId)
            ->streaming(true)
            ->go();

        // In a real application, you would iterate through the stream
        // and send chunks to the frontend as they arrive
        return $stream;

        // Example of iterating through stream:
        // foreach ($stream as $chunk) {
        //     // Send $chunk to frontend via WebSockets, etc.
        // }
    }
}
