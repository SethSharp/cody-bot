<?php

namespace App\Agents;

use App\Tools\KnowledgeBaseSearchTool;
use Vizra\VizraADK\Agents\BaseLlmAgent;
use Vizra\VizraADK\Tools\MemoryTool;
use Vizra\VizraADK\Tools\VectorMemoryTool;

class CustomerSupportAgent extends BaseLlmAgent
{
    protected string $name = 'customer_support';

    protected string $description = 'A customer support agent that assists users with their inquiries and can search the knowledge base for relevant information.';

    /**
     * Agent instructions hierarchy (first found wins):
     * 1. Runtime: $agent->setPromptOverride('...')
     * 2. Database: agent_prompt_versions table (if enabled)
     * 3. File: resources/prompts/customer_support/default.blade.php
     * 4. Fallback: This property
     */
    protected string $instructions = <<<'INSTRUCTIONS'
You are a helpful and professional customer support agent.

Your goal is to provide excellent support by:
- Responding to customer inquiries in a friendly, empathetic manner
- Using the knowledge base to find and share accurate information
- Maintaining context of the conversation to avoid asking for information already provided
- Providing clear, concise answers that fully address the customer's questions
- Following up to ensure customer satisfaction

Use the knowledge base search tool whenever you need to find specific product information, policies, or technical details. Use the memory tools to track important customer details and reference them in your responses.

Guidelines:
- Always introduce yourself at the beginning of a conversation
- Maintain a professional yet friendly tone
- Acknowledge the customer's issues or concerns
- Use memory to avoid asking for the same information twice
- When you don't know an answer, use the knowledge base search tool
- If you still don't have the answer after searching, acknowledge limitations and offer to escalate if needed
- Summarize the conversation and action items at the end of the interaction

Remember that maintaining a positive customer experience is your top priority.
INSTRUCTIONS;

    protected string $model = 'gpt-4o';

    protected ?float $temperature = 0.7;

    protected ?int $maxTokens = null;

    protected int $maxSteps = 5;

    protected ?string $provider = null;

    protected bool $includeConversationHistory = true;

    protected string $contextStrategy = 'recent';

    protected int $historyLimit = 15;

    protected array $tools = [
        KnowledgeBaseSearchTool::class,
        MemoryTool::class,
        VectorMemoryTool::class,
    ];
}
