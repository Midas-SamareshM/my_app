# PHP Coding Standards

Full reference for code conventions used in this project.

---

## 1. File Header

Every PHP file starts with:

```php
<?php

declare(strict_types=1);

namespace App\Feature;
```

`declare(strict_types=1)` must be the first statement after `<?php`. It enforces strict type checking on all function arguments and return values.

---

## 2. Naming Conventions

### Variables — camelCase, always meaningful

```php
// Bad
$d   = new DateTime();
$arr = getUserData();
$tmp = $request->getQueryParams();

// Good
$createdAt      = new DateTime();
$userData       = getUserData();
$queryParams    = $request->getQueryParams();
$invoiceAmount  = calculateInvoiceTotal($lineItems);
```

**Rules:**
- Minimum 3 characters (except loop counters `$i`, `$j`)
- Describes *what* it holds, not its type (`$userList` not `$array`)
- Boolean variables: prefix with `is`, `has`, `can` — `$isActive`, `$hasPermission`

### Functions & Methods — camelCase

```php
// Bad
function GetUserById(int $id) { }
function get_user_by_id(int $id) { }
function getuser(int $id) { }

// Good
function getUserById(int $userId): ?User { }
function calculateMonthlyRevenue(int $month, int $year): float { }
function isEmailVerified(int $userId): bool { }
```

**Common prefixes:**
| Prefix | Use |
|--------|-----|
| `get`  | Returns a value |
| `find` | Returns value or null |
| `create` | Creates and returns new instance |
| `update` | Modifies and returns updated instance |
| `delete` / `remove` | Deletes, returns void or bool |
| `is` / `has` / `can` | Returns bool |
| `calculate` / `compute` | Derives a value |
| `handle` / `process` | Performs an action |

### Classes — PascalCase

```php
class UserRepository { }
class InvoiceService { }
class SendWelcomeEmailJob { }
class UserRepositoryInterface { }   // interfaces: suffix with Interface
class BaseController { }            // abstract base: prefix with Base
```

### Constants — UPPER_SNAKE_CASE

```php
const MAX_LOGIN_ATTEMPTS = 5;
const DEFAULT_PAGE_SIZE  = 20;
const CACHE_TTL_SECONDS  = 3600;
```

---

## 3. Docblocks

Every public and protected function/method requires a full docblock.

### Structure

```php
/**
 * [Short one-line summary ending with a period.]
 *
 * [Optional longer description if the behavior is non-obvious.
 * Can span multiple lines.]
 *
 * @param  type  $paramName  Description of the parameter.
 * @param  type  $paramName  Description of the parameter.
 *
 * @return type  Description of what is returned.
 *
 * @throws ExceptionClass  When/why this exception is thrown.
 */
```

### Examples

```php
/**
 * Find a user by their primary key.
 *
 * @param  int  $userId  The user's database ID.
 *
 * @return User|null  The matching user, or null if not found.
 *
 * @throws DatabaseException  If the query fails unexpectedly.
 */
public function findById(int $userId): ?User
{
    // ...
}

/**
 * Calculate the total price of an order including tax.
 *
 * @param  array<int, LineItem>  $lineItems   List of order line items.
 * @param  float                 $taxRate     Tax rate as a decimal (e.g. 0.15 for 15%).
 *
 * @return float  Total price including tax, rounded to 2 decimal places.
 */
public function calculateTotal(array $lineItems, float $taxRate): float
{
    // ...
}

/**
 * Send a password reset email to the given address.
 *
 * @param  string  $emailAddress  Recipient email address.
 * @param  string  $resetToken    Single-use reset token.
 *
 * @return void
 *
 * @throws MailerException  If the email cannot be dispatched.
 */
public function sendPasswordResetEmail(string $emailAddress, string $resetToken): void
{
    // ...
}
```

### Type hints in docblocks

| PHP type | Docblock |
|----------|----------|
| `?User` | `User\|null` |
| `array` of strings | `array<int, string>` |
| `array` of objects | `array<int, User>` |
| callable | `callable(int $id): string` |
| mixed | `mixed` (use sparingly) |

---

## 4. Return Types

Return types are **always** declared explicitly.

```php
// Bad — no return type
public function getUser(int $userId)
{
    return $this->db->find($userId);
}

// Good
public function getUser(int $userId): ?User
{
    return $this->db->find($userId);
}
```

### Common return types

```php
function getName(): string { }
function getAge(): int { }
function getPrice(): float { }
function isActive(): bool { }
function getItems(): array { }        // use array<K,V> in docblock for specifics
function getUser(): ?User { }         // nullable
function createResponse(): ResponseInterface { }
function processQueue(): void { }
function findOrFail(): User { }       // throws if not found, never null
```

---

## 5. PSR-7 HTTP Messages

PSR-7 defines immutable HTTP message interfaces. Never use superglobals directly.

### Immutability rule

PSR-7 objects are immutable. `with*()` methods return a **new** instance:

```php
// Wrong — return value is discarded
$response->withHeader('Content-Type', 'application/json');

// Correct — capture the new instance
$response = $response->withHeader('Content-Type', 'application/json');
```

### Reading a request

```php
use Psr\Http\Message\ServerRequestInterface;

public function handle(ServerRequestInterface $request): ResponseInterface
{
    // Query string: ?page=2&limit=20
    $queryParams = $request->getQueryParams();
    $currentPage = (int) ($queryParams['page'] ?? 1);
    $pageLimit   = min((int) ($queryParams['limit'] ?? 20), 100);

    // POST body (parsed)
    $bodyParams   = $request->getParsedBody();
    $emailAddress = (string) ($bodyParams['email'] ?? '');

    // Route attribute (set by router)
    $userId = (int) $request->getAttribute('id');

    // Headers
    $authHeader = $request->getHeaderLine('Authorization');

    // Uploaded files
    $uploadedFiles = $request->getUploadedFiles();
    $avatarFile    = $uploadedFiles['avatar'] ?? null;
}
```

### Building a response

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;

public function jsonResponse(
    ResponseFactoryInterface $responseFactory,
    StreamFactoryInterface   $streamFactory,
    mixed                    $data,
    int                      $statusCode = 200
): ResponseInterface {
    $jsonBody = json_encode($data, JSON_THROW_ON_ERROR);
    $stream   = $streamFactory->createStream($jsonBody);

    return $responseFactory
        ->createResponse($statusCode)
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withBody($stream);
}
```

### PSR-7 interface quick reference

| Interface | Purpose |
|-----------|---------|
| `ServerRequestInterface` | Incoming HTTP request (extends `RequestInterface`) |
| `ResponseInterface` | HTTP response |
| `StreamInterface` | Request/response body |
| `UriInterface` | URI value object |
| `UploadedFileInterface` | File upload |
| `RequestInterface` | Outgoing request (for HTTP clients) |

### Superglobal replacements

| Superglobal | PSR-7 replacement |
|-------------|-------------------|
| `$_GET` | `$request->getQueryParams()` |
| `$_POST` | `$request->getParsedBody()` |
| `$_FILES` | `$request->getUploadedFiles()` |
| `$_COOKIE` | `$request->getCookieParams()` |
| `$_SERVER['HTTP_*']` | `$request->getHeaderLine('Header-Name')` |
| `$_SERVER['REQUEST_METHOD']` | `$request->getMethod()` |
| `$_SERVER['REQUEST_URI']` | `(string) $request->getUri()` |

---

## 6. Full Example

```php
<?php

declare(strict_types=1);

namespace App\User;

use App\User\User;
use App\User\UserRepositoryInterface;
use App\Exceptions\UserNotFoundException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class UserController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {}

    /**
     * Retrieve a single user by their ID.
     *
     * @param  ServerRequestInterface  $request  The incoming HTTP request.
     *
     * @return ResponseInterface  JSON response with user data or 404 error.
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $userId = (int) $request->getAttribute('id');

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            return $this->buildJsonResponse(['error' => 'User not found.'], 404);
        }

        return $this->buildJsonResponse($user->toArray(), 200);
    }

    /**
     * Build a JSON HTTP response.
     *
     * @param  array<string, mixed>  $data        Response payload.
     * @param  int                   $statusCode  HTTP status code.
     *
     * @return ResponseInterface  The constructed response.
     */
    private function buildJsonResponse(array $data, int $statusCode): ResponseInterface
    {
        $jsonBody = json_encode($data, JSON_THROW_ON_ERROR);
        $stream   = $this->streamFactory->createStream($jsonBody);

        return $this->responseFactory
            ->createResponse($statusCode)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody($stream);
    }
}
```
