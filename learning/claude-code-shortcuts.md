# Claude Code Shortcuts & Tips

---

## CLI Basics

```bash
# Start a session in a project directory
claude

# Run a one-off command without entering interactive mode
claude -p "explain the auth flow in this codebase"

# Run a command, then continue interactively
claude --continue "add tests for UserController"

# Point to a specific directory
claude --dir /path/to/project
```

---

## In-Session Commands

| Command | What it does |
|---------|--------------|
| `/help` | Show all built-in commands |
| `/clear` | Clear conversation context |
| `/compact` | Summarize and compress context |
| `/config` | Open settings (theme, model, etc.) |
| `/fast` | Toggle fast mode (Opus with faster output) |
| `/exit` or `Ctrl+C` | Exit Claude Code |

---

## Running Shell Commands

Prefix with `!` to run a shell command directly in the session:

```
! git status
! npm run test
! php artisan migrate
! gcloud auth login
```

The output appears in the conversation so Claude can see and act on it.

---

## Permission Modes

Claude Code asks for permission before running tools. Modes:

| Mode | Behavior |
|------|----------|
| Default | Prompts for most tool calls |
| Auto-approve | Approves read-only operations silently |
| Yolo | Approves everything (use carefully) |

To reduce permission prompts for common commands, use `/update-config` or the `/fewer-permission-prompts` skill.

---

## Keyboard Shortcuts (defaults)

| Shortcut | Action |
|----------|--------|
| `Enter` | Submit message |
| `Shift+Enter` | New line in message |
| `Up/Down` | Navigate message history |
| `Ctrl+C` | Cancel current operation |
| `Ctrl+L` | Clear screen |

Customize with `/keybindings-help`.

---

## CLAUDE.md — Project Config

Place a `CLAUDE.md` file at the project root. Claude reads it at the start of every session.

Use it for:
- Project overview and architecture notes
- Coding conventions and style rules
- Forbidden actions ("never drop this table", "always run migrations with --pretend first")
- Common commands (build, test, deploy)

Generate an initial one with `/init`.

---

## Memory System

Claude Code has a persistent memory system at `~/.claude/projects/<project>/memory/`.

Claude auto-saves:
- User preferences and role
- Feedback on its own behavior
- Project context and decisions
- External resource pointers

To trigger manually: "Remember that we always use cursor-based pagination here."

---

## Hooks

Hooks run shell commands automatically on events (file save, tool call, session end).

Examples:
- Run linter after every file edit
- Show a notification when Claude finishes a task
- Auto-format code on write

Configure via `/update-config`.

---

## MCP Servers

Claude Code supports Model Context Protocol (MCP) servers to extend its tools:
- Connect to databases, APIs, file systems
- Add custom tools Claude can call
- Share tool servers across projects

Configure in `~/.claude/settings.json` under `mcpServers`.
