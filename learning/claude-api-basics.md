# Claude API Basics

Quick reference for using the Claude API / Anthropic SDK.

> For up-to-date details, use the `/claude-api` skill inside Claude Code.

---

## Models (as of 2026)

| Model | ID | Best For |
|-------|----|----------|
| Claude Opus 4.8 | `claude-opus-4-8` | Complex reasoning, long tasks |
| Claude Sonnet 4.6 | `claude-sonnet-4-6` | Balanced speed + quality (default) |
| Claude Haiku 4.5 | `claude-haiku-4-5-20251001` | Fast, lightweight tasks |

**Rule of thumb:** Start with Sonnet. Move to Opus for hard reasoning tasks. Use Haiku for high-throughput / low-latency needs.

---

## Basic Usage (Node.js)

```js
import Anthropic from "@anthropic-ai/sdk";

const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

const message = await client.messages.create({
  model: "claude-sonnet-4-6",
  max_tokens: 1024,
  messages: [{ role: "user", content: "Explain async/await in JS." }],
});

console.log(message.content[0].text);
```

---

## Basic Usage (PHP)

```php
// No official PHP SDK — use HTTP directly
$response = Http::withHeaders([
    'x-api-key' => env('ANTHROPIC_API_KEY'),
    'anthropic-version' => '2023-06-01',
    'content-type' => 'application/json',
])->post('https://api.anthropic.com/v1/messages', [
    'model' => 'claude-sonnet-4-6',
    'max_tokens' => 1024,
    'messages' => [
        ['role' => 'user', 'content' => 'Hello!'],
    ],
]);

echo $response->json('content.0.text');
```

---

## System Prompts

```js
await client.messages.create({
  model: "claude-sonnet-4-6",
  max_tokens: 1024,
  system: "You are a helpful PHP/Laravel assistant. Be concise.",
  messages: [{ role: "user", content: "How do I eager load in Eloquent?" }],
});
```

---

## Key Parameters

| Parameter | Type | Notes |
|-----------|------|-------|
| `model` | string | Required |
| `max_tokens` | int | Required — controls response length |
| `messages` | array | Required — conversation history |
| `system` | string | Optional — sets assistant behavior |
| `temperature` | float | 0–1, default 1. Lower = more deterministic |
| `stream` | bool | Enable streaming responses |

---

## Tool Use (Function Calling)

```js
const tools = [{
  name: "get_weather",
  description: "Get current weather for a city",
  input_schema: {
    type: "object",
    properties: {
      city: { type: "string", description: "City name" },
    },
    required: ["city"],
  },
}];

await client.messages.create({
  model: "claude-sonnet-4-6",
  max_tokens: 1024,
  tools,
  messages: [{ role: "user", content: "What's the weather in Dhaka?" }],
});
```

---

## Prompt Caching

Add `cache_control` to cache large, repeated context (e.g. long system prompts, documents):

```js
system: [{
  type: "text",
  text: "... long system prompt ...",
  cache_control: { type: "ephemeral" },
}]
```

Caches last up to 5 minutes. Reduces cost and latency on repeated calls.

---

## Streaming

```js
const stream = client.messages.stream({
  model: "claude-sonnet-4-6",
  max_tokens: 1024,
  messages: [{ role: "user", content: "Write a short story." }],
});

for await (const chunk of stream) {
  if (chunk.type === "content_block_delta") {
    process.stdout.write(chunk.delta.text);
  }
}
```

---

## Error Handling

| Status | Meaning |
|--------|---------|
| 400 | Bad request (invalid params) |
| 401 | Invalid API key |
| 429 | Rate limited — back off and retry |
| 529 | API overloaded — retry with exponential backoff |

---

## Resources

- Docs: https://docs.anthropic.com
- SDK (Node): `npm install @anthropic-ai/sdk`
- Use `/claude-api` skill in Claude Code for live reference
