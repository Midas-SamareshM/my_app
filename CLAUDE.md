# Project: my_app

XAMPP-based PHP web application.

---

## Stack

- PHP (via XAMPP)
- MySQL (via XAMPP)
- Apache web server
- Project root: `C:\xampp\htdocs\my_app`

---

## Common Commands

```bash
# Start XAMPP services (run as admin if needed)
C:\xampp\xampp_start.exe

# Access app
http://localhost/my_app

# Access phpMyAdmin
http://localhost/phpmyadmin
```

---

## Coding Standards (enforced on every file)

These rules apply to ALL PHP code written in this project. No exceptions.

### Naming

- **Variables**: camelCase, always meaningful — `$userEmailAddress` not `$e` or `$data`
- **Functions/Methods**: camelCase — `getUserById()` not `get_user_by_id()` or `getuser()`
- **Classes**: PascalCase — `UserRepository`, `OrderService`
- **Constants**: UPPER_SNAKE_CASE — `MAX_RETRY_COUNT`
- **Interfaces**: PascalCase, suffix with `Interface` — `UserRepositoryInterface`

### Docblocks — required on every function/method

Every function must have a docblock with:
- One-line summary
- `@param` for each parameter (type + name + description)
- `@return` with type and description
- `@throws` if the function can throw

```php
/**
 * Find a user by their primary key.
 *
 * @param  int  $userId  The user's database ID.
 * @return User|null     The matching user, or null if not found.
 * @throws DatabaseException  If the query fails.
 */
public function findById(int $userId): ?User
```

### Return Types — required on every function/method

- Always declare explicit return types: `string`, `int`, `bool`, `array`, `?User`, `void`, etc.
- Never omit the return type and rely on inference.
- Use union types when necessary: `int|false`, `User|null` (prefer `?User` shorthand for nullable).

### PSR-7 (HTTP Messages)

All HTTP request/response handling must follow PSR-7:
- Use `Psr\Http\Message\ServerRequestInterface` for incoming requests — never access `$_GET`, `$_POST`, `$_SERVER` directly
- Use `Psr\Http\Message\ResponseInterface` for responses — never `echo` or `header()` directly
- Requests and responses are **immutable** — always use `with*()` methods and capture the returned instance
- Stream bodies via `Psr\Http\Message\StreamInterface`

```php
// Correct PSR-7 pattern
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $queryParams = $request->getQueryParams();
    $userId      = (int) ($queryParams['id'] ?? 0);

    $user = $this->userRepository->findById($userId);

    $body = $this->streamFactory->createStream(json_encode($user));

    return $this->responseFactory
        ->createResponse(200)
        ->withHeader('Content-Type', 'application/json')
        ->withBody($body);
}
```

### General Rules

- Use strict types in every file: `declare(strict_types=1);`
- Keep files organized by feature, not by type
- Use prepared statements for all DB queries — no raw string concatenation
- Validate all user input at the request handler layer
- No magic numbers — use named constants

---

## Detailed Reference

- `learning/coding-standards.md` — full PSR-7 patterns, docblock examples, naming guide
- `learning/prompting.md` — prompting tips
- `learning/claude-api-basics.md` — Claude API reference
- `learning/claude-code-shortcuts.md` — CLI shortcuts and hooks
- `skills.md` — Claude Code slash commands
