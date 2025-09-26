<?php

namespace App\Agents;

use Prism\Prism\Text\PendingRequest;
use Vizra\VizraADK\Agents\BaseLlmAgent;
use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\System\AgentContext;
use Vizra\VizraADK\Tools\MemoryTool;

class PrReview extends BaseLlmAgent
{
    protected string $name = 'pr_review';
    protected string $description = 'An expert code reviewer that analyzes pull requests, provides detailed feedback, and tracks conversation context.';

    /**
     * Agent instructions hierarchy (first found wins):
     * 1. Runtime: $agent->setPromptOverride('...')
     * 2. Database: agent_prompt_versions table (if enabled)
     * 3. File: resources/prompts/pr_review/default.blade.php
     * 4. Fallback: This property
     *
     * The prompt file has been created for you at:
     * resources/prompts/pr_review/default.blade.php
     */
    protected string $instructions = 'You are an expert code reviewer specialized in analyzing pull requests. You can:

    1. Read code files to understand implementations
    2. Search for patterns and potential issues
    3. Suggest improvements based on best practices
    4. Track context across the conversation to provide consistent feedback
    5. Use GitHub to retrieve PR information and comment on PRs

    Always provide constructive feedback with specific examples and explain the reasoning behind your recommendations.

    For memory management:
    1. Store important information about the PR being reviewed
    2. Remember previously discussed issues and comments
    3. Track user preferences for review style and focus areas
    4. Build context over time to provide more personalized and consistent reviews';

    protected string $model = 'gpt-4o';
    protected bool $includeConversationHistory = true;
    protected string $contextStrategy = 'recent';
    protected int $historyLimit = 15;

    protected array $mcpServers = [
        'filesystem',
        'github'
    ];

    protected array $tools = [
        MemoryTool::class,
    ];

    /*

    Optional hook methods to override:

    public function beforeLlmCall(array $inputMessages, AgentContext $context): array
    {
        // $context->setState('custom_data_for_llm', 'some_value');
        // $inputMessages[] = ['role' => 'system', 'content' => 'Additional system note for this call.'];
        return parent::beforeLlmCall($inputMessages, $context);
    }

    public function afterLlmResponse(mixed $response, AgentContext $context, ?PendingRequest $request = null): mixed {

         return parent::afterLlmResponse($response, $context, $request);

    }

    public function beforeToolCall(string $toolName, array $arguments, AgentContext $context): array {

        return parent::beforeToolCall($toolName, $arguments, $context);

    }

    public function afterToolResult(string $toolName, string $result, AgentContext $context): string {

        return parent::afterToolResult($toolName, $result, $context);

    } */
}
