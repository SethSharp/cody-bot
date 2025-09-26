<?php

namespace App\Tools;

use Illuminate\Support\Facades\DB;
use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\Memory\AgentMemory;
use Vizra\VizraADK\System\AgentContext;
use Vizra\VizraADK\Services\VectorMemoryManager;

class KnowledgeBaseSearchTool implements ToolInterface
{
    /**
     * Define the tool's schema for the LLM
     */
    public function definition(): array
    {
        return [
            'name' => 'knowledge_base_search',
            'description' => 'Search the knowledge base for relevant information to answer customer questions',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'The search query to find relevant information in the knowledge base',
                    ],
                    'limit' => [
                        'type' => 'number',
                        'description' => 'Maximum number of results to return',
                        'default' => 5,
                    ],
                ],
                'required' => ['query'],
            ],
        ];
    }

    /**
     * Execute the tool with given arguments
     *
     * @param array $arguments The parameters passed by the LLM
     * @param AgentContext $context Current execution context
     * @param AgentMemory $memory Agent's memory instance
     * @return string JSON-encoded result
     */
    public function execute(array $arguments, AgentContext $context, AgentMemory $memory): string
    {
        $query = $arguments['query'];
        $limit = $arguments['limit'] ?? 5;

        try {
            $vectorManager = app(VectorMemoryManager::class);

            // Perform semantic search in the vector database
            $results = $vectorManager->search($query, limit: $limit);

            // If no results found in vector database, fallback to keyword search
            if (empty($results)) {
                // Simple keyword search in a knowledge_base table (assuming it exists)
                // In a production environment, this could be replaced with a more sophisticated search
                $results = $this->fallbackKeywordSearch($query, $limit);
            }

            // Track search in memory for future reference
            $memory->remember('last_search_query', $query);
            $memory->remember('last_search_time', now()->toDateTimeString());

            return json_encode([
                'status' => 'success',
                'count' => count($results),
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Search failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Fallback to a basic keyword search if vector search returns no results
     *
     * @param string $query The search query
     * @param int $limit Maximum number of results
     * @return array Search results
     */
    private function fallbackKeywordSearch(string $query, int $limit): array
    {
        // This is a simplified example - in a real implementation,
        // you would search an actual knowledge base table/collection

        // Simulate knowledge base entries for demonstration
        $knowledgeEntries = [
            [
                'id' => 1,
                'title' => 'How to reset your password',
                'content' => 'To reset your password, click on the "Forgot Password" link on the login page and follow the instructions sent to your email.',
                'category' => 'account',
            ],
            [
                'id' => 2,
                'title' => 'Subscription plans',
                'content' => 'We offer three subscription plans: Basic, Pro, and Enterprise. Each plan includes different features and support levels.',
                'category' => 'billing',
            ],
            [
                'id' => 3,
                'title' => 'Cancellation policy',
                'content' => 'You can cancel your subscription at any time. Refunds are processed according to our refund policy.',
                'category' => 'billing',
            ],
            [
                'id' => 4,
                'title' => 'Contact support',
                'content' => 'For immediate assistance, you can reach our support team via live chat, email at support@example.com, or call us at (555) 123-4567.',
                'category' => 'support',
            ],
            [
                'id' => 5,
                'title' => 'System requirements',
                'content' => 'Our platform works best on Chrome, Firefox, Safari, and Edge browsers. Make sure your browser is updated to the latest version.',
                'category' => 'technical',
            ],
        ];

        // Simple keyword search through the entries
        $results = [];
        foreach ($knowledgeEntries as $entry) {
            $matchScore = 0;

            // Check for keyword matches in title and content
            if (stripos($entry['title'], $query) !== false) {
                $matchScore += 2; // Title match is weighted higher
            }

            if (stripos($entry['content'], $query) !== false) {
                $matchScore += 1;
            }

            if ($matchScore > 0) {
                $results[] = [
                    'id' => $entry['id'],
                    'title' => $entry['title'],
                    'content' => $entry['content'],
                    'category' => $entry['category'],
                    'score' => $matchScore,
                ];
            }
        }

        // Sort by score (highest first) and limit results
        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($results, 0, $limit);
    }
}
