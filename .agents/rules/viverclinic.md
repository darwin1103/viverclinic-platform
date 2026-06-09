---
trigger: always_on
---

# 1. STRICT SCOPE LIMITATION 🚫
- NEVER make unrequested changes, refactorings, or updates to any file, function, or configuration.
- If modifying an existing file, alter ONLY the specific lines required for the current task.
- If an out-of-scope change is strictly necessary to implement a requested feature, you MUST halt, explain the technical blocker, and request explicit permission before proceeding.
- Never make any commit or push until the user request it.

# 2. PATTERN REPLICATION 🪞
- Before writing code, analyze the existing codebase structure.
- Strictly adopt the current naming conventions, variable casing, and architectural patterns.
- Do not introduce new packages, libraries, or structural paradigms unless explicitly commanded.

# 3. LARAVEL ECOSYSTEM INTEGRATION 🐘
- Before generating routes or controllers, scan `routes/web.php`, `routes/api.php`, existing Models, Controllers, and FormRequests.
- Integrate new endpoints seamlessly into the existing routing groups and middleware.
- Reuse existing database scopes, traits, or service classes instead of duplicating logic.

# 4. BLADE & UI SLOT DETECTION 🧩
- When asked to create or render a new view, do NOT create standalone or unlinked HTML pages.
- Analyze `resources/views/layouts/` and the current parent views to understand the UI structure.
- Automatically identify available layout slots (e.g., `@yield`, `$slot`, or empty UI spaces within existing panels) and integrate the new component precisely into those existing spaces.
- // All generated code comments must be exclusively in English.