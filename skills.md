# Claude Code Skills Reference

A quick-reference guide to built-in Claude Code skills (slash commands).

---

## Core Skills

### `/code-review`
Review the current diff for bugs and cleanup opportunities.

```
/code-review          # default (medium effort)
/code-review high     # broader coverage
/code-review --fix    # apply findings automatically
/code-review --comment  # post as inline PR comments
/code-review ultra    # deep multi-agent cloud review
```

### `/simplify`
Review changed code for reuse, simplification, and efficiency — then apply fixes.

```
/simplify
```

### `/security-review`
Full security review of pending changes on the current branch.

```
/security-review
```

### `/run`
Launch the app and confirm a change works in the real app (not just tests).

```
/run
```

### `/verify`
Verify a code change does what it's supposed to by running and observing behavior.

```
/verify
```

### `/review`
Review a pull request.

```
/review
/review 123    # review PR #123
```

---

## Setup & Config Skills

### `/init`
Initialize a new `CLAUDE.md` file with codebase documentation.

```
/init
```

### `/update-config`
Configure Claude Code harness via `settings.json`. Use for:
- Automated behaviors ("from now on when X...")
- Permissions ("allow npm commands")
- Environment variables ("set DEBUG=true")
- Hook troubleshooting

```
/update-config
```

### `/keybindings-help`
Customize keyboard shortcuts or modify `~/.claude/keybindings.json`.

```
/keybindings-help
```

---

## Scheduling & Loops

### `/schedule`
Create or manage scheduled remote agents (cron-based routines).

```
/schedule        # create/manage schedules
```

### `/loop`
Run a prompt or slash command on a recurring interval.

```
/loop 5m /code-review    # run /code-review every 5 minutes
/loop                     # self-paced loop
```

---

## API & Reference

### `/claude-api`
Reference for the Claude API / Anthropic SDK — models, pricing, params, streaming, tool use, MCP.

```
/claude-api
```

---

## Tips

- Skills are invoked with `/skill-name` in the Claude Code prompt.
- Most skills read the current working directory and git state automatically.
- Use `/help` inside Claude Code for the built-in command list.
- Feedback and bugs: https://github.com/anthropics/claude-code/issues

---

## Reusable PHP Patterns (this project)

These are the reusable base classes and traits already built into this project.
When adding new features, extend these rather than writing from scratch.

### `BaseModel` — `src/Models/BaseModel.php`

Extend for any new DB table. Provides CRUD automatically.

```php
class Category extends BaseModel
{
    protected string $table = 'categories';  // only this is required

    // Inherited for free: findById, getAll, create, update, delete, count
    // For custom queries use: $this->query($sql, $params)
    //                         $this->queryOne($sql, $params)
}
```

**How to add a new model:**
```
Ask Claude: "Create a model for the `reviews` table extending BaseModel"
```

---

### `BaseController` — `src/Controllers/BaseController.php`

Extend for any new controller. Provides rendering, redirects, and auth guards.

```php
class ReviewController extends BaseController
{
    // Inherited for free:
    // $this->render('view/path', $data)          — renders view in layout
    // $this->redirect('/path')                   — redirect + exit
    // $this->redirectWithMessage('/path', 'success', 'Done!') — redirect + flash
    // $this->requireAuth()                       — 403 if not logged in
    // $this->requireAdmin()                      — 403 if not admin
    // $this->requireValidCsrf()                  — 403 if CSRF invalid
    // $this->json($data, 200)                    — JSON response + exit
}
```

**How to add a new controller + routes:**
```
Ask Claude: "Add a ReviewController with index and create actions,
following the BaseController pattern, and register routes in config/routes.php"
```

---

### `Uploadable` trait — `src/Traits/Uploadable.php`

Add to any model that handles file uploads.

```php
class Review extends BaseModel
{
    use Uploadable;

    // Now available:
    // $this->uploadImage($fileDescriptor, 'reviews')   → returns stored path
    // $this->deleteUploadedFile($storedPath)           → deletes file from disk
}
```

---

### Global helpers — `src/Helpers/functions.php`

Available everywhere (no import needed):

| Function | Purpose |
|----------|---------|
| `render('view/path', $data)` | Render a view file |
| `redirect('/path')` | Redirect and exit |
| `url('/path')` | Build absolute URL |
| `uploadUrl($storedPath)` | Build upload file URL |
| `e($value)` | HTML-escape output |
| `csrfField()` | Render hidden CSRF input |
| `verifyCsrf($token)` | Validate CSRF token |
| `formatPrice($amount)` | Format as currency string |
| `slugify($text)` | Convert text to URL slug |

---

### Auth & Flash helpers

```php
use App\Helpers\Auth;

Auth::login($userId, $role);   // persist login
Auth::logout();                // destroy session
Auth::isLoggedIn(): bool
Auth::userId(): ?int
Auth::isAdmin(): bool

use App\Helpers\Flash;

Flash::set('success', 'Done!');   // store one-shot message
Flash::get(): ?array              // retrieve + clear
```

---

### Router — `src/Router.php`

Register routes in `config/routes.php`:

```php
$router->get('/reviews',            [ReviewController::class, 'index']);
$router->get('/reviews/{id}',       [ReviewController::class, 'show']);
$router->post('/reviews/create',    [ReviewController::class, 'create']);
$router->post('/reviews/{id}/delete', [ReviewController::class, 'delete']);
```

`{id}` is passed as a `string` argument to the controller method.

---

### Adding a complete new feature — checklist

1. Schema: add table to `database/schema.sql`
2. Model: extend `BaseModel`, add custom query methods
3. Controller: extend `BaseController`, add action methods
4. Routes: register in `config/routes.php`
5. Views: create in `views/feature-name/`

**Prompt to use:**
```
"Add a product reviews feature. Users can leave a rating (1-5) and comment
on any product. Follow the BaseModel / BaseController / Uploadable patterns
already in the project. Add routes to config/routes.php."
```
