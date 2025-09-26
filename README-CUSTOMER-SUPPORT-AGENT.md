# Customer Support Agent

A Vizra ADK-powered customer support agent that uses memory to track conversation context and can search a knowledge base to provide accurate information.

## Features

- **Conversation Context Tracking**: Remembers previous interactions with users to provide a seamless experience
- **Knowledge Base Search**: Can search for information to answer customer questions accurately
- **Memory Persistence**: Maintains user preferences and important details across conversations
- **Session-based Conversations**: Supports multiple conversation threads with the same user
- **Streaming Responses**: Provides real-time response chunks for a better user experience
- **Customizable Context**: Accept additional parameters to provide more context to the agent

## Implementation

The implementation consists of:

1. **CustomerSupportAgent**: The main agent class that handles customer interactions
2. **KnowledgeBaseSearchTool**: A tool for searching the knowledge base
3. **Default Prompt Template**: A Blade template for the agent's instructions that can include user information
4. **Example Usage Class**: Examples of how to use the agent in different scenarios
5. **Feature Tests**: Tests to ensure proper functionality of the agent

## Usage

### Basic Usage

```php
use App\Agents\CustomerSupportAgent;

// Simple query
$response = CustomerSupportAgent::run('How do I reset my password?')->go();
```

### With User Context (for Memory Persistence)

```php
// Run with user context to enable persistent memory
$response = CustomerSupportAgent::run($customerQuery)
    ->forUser($user)
    ->go();
```

### With Session Persistence

```php
// Generate a unique session ID for a conversation thread
$sessionId = 'support-ticket-' . $ticketId;

// Run with both user context and session ID
$response = CustomerSupportAgent::run($customerQuery)
    ->forUser($user)
    ->withSession($sessionId)
    ->go();
```

### With Additional Context

```php
// Pass additional context parameters
$response = CustomerSupportAgent::run($customerQuery)
    ->forUser($user)
    ->withSession($sessionId)
    ->withParameters([
        'context' => 'Customer reached out about billing issue',
        'priority' => 'high',
        'metadata' => ['ticket_id' => $ticketId],
    ])
    ->go();
```

### Streaming Responses

```php
// Enable streaming for real-time UI updates
$stream = CustomerSupportAgent::run($customerQuery)
    ->forUser($user)
    ->withSession($sessionId)
    ->streaming(true)
    ->go();

// Process the stream (in controller or job)
foreach ($stream as $chunk) {
    // Send chunk to frontend (e.g., via WebSockets)
    broadcast(new MessageChunk($chunk, $conversationId));
}
```

## Knowledge Base

The knowledge base search tool provides two search mechanisms:

1. **Vector Search**: Semantic search using embeddings (for more accurate, meaning-based matches)
2. **Keyword Search**: Fallback search when vector search doesn't find matches

In a production environment, you would:

1. Configure the vector database with your knowledge base articles
2. Replace the fallback implementation with a database query to your actual knowledge base

## Customizing the Agent

### Modifying the Instructions

Edit the Blade template at:
```
resources/prompts/customer_support/default.blade.php
```

### Changing the Knowledge Base

Modify the `fallbackKeywordSearch` method in `KnowledgeBaseSearchTool.php` to connect to your actual knowledge base storage.

### Adding New Tools

Add additional tools to the `$tools` array in the `CustomerSupportAgent` class to extend capabilities.

## Testing

Run the tests to verify the agent is functioning correctly:

```bash
php artisan test --filter=CustomerSupportAgentTest
```

## Dependencies

- Laravel 12+
- Vizra ADK package
- Database for vector storage (optional but recommended)
