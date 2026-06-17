# Effective Prompting with Claude

---

## Core Principles

### Be specific about the task
Vague: "Fix the bug"
Better: "Fix the null pointer in `UserService.getById()` — it crashes when the user ID doesn't exist in the DB"

### Provide context upfront
Tell Claude:
- What the code is supposed to do
- What is actually happening
- Any constraints (performance, backwards compat, etc.)

### Ask for what you want, not how to do it
Let Claude choose the implementation. Focus your prompt on the *outcome*.

---

## Prompt Patterns

### Explain code
```
Explain what this function does and why it works this way.
```

### Find bugs
```
Review this function for bugs. Focus on edge cases with null/empty inputs.
```

### Refactor
```
Refactor this to remove duplication. Don't change behavior.
```

### Add a feature
```
Add pagination to the /users endpoint. Use cursor-based pagination. 
Page size default: 20, max: 100.
```

### Write tests
```
Write unit tests for UserService. Cover the happy path and these edge cases: [list them].
```

### Debug
```
This throws "Cannot read property 'id' of undefined" on line 42. Here's the stack trace: [paste it].
```

---

## What Claude Does Well

- Reading and understanding unfamiliar codebases quickly
- Refactoring and simplifying complex code
- Writing tests for existing code
- Explaining technical concepts at any level
- Finding subtle bugs (off-by-one, race conditions, null refs)
- Translating requirements into working code

## What to Watch Out For

- Long context: very large files may be partially read — point Claude to the relevant section
- Hallucinated APIs: always verify function/method names exist in your version
- Over-engineering: Claude may add abstractions you didn't ask for — be explicit ("keep it simple")
- Test assertions: generated tests may assert the wrong behavior — review them

---

## Iterating with Claude

1. Start with a clear, scoped request
2. Review the output — don't blindly accept it
3. Give specific feedback: "that's correct but remove the extra logging"
4. Ask follow-up questions in the same session (context is preserved)

---

## Useful Phrases

| Goal | Phrase |
|------|--------|
| Scope the change | "Only change X, don't touch Y" |
| Keep it simple | "No new abstractions, minimal changes" |
| Explain reasoning | "Tell me why you did it this way" |
| Alternative approach | "What's another way to do this?" |
| Verify before change | "What would you change before making it?" |
