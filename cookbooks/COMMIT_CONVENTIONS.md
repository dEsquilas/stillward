# Stillward — Commit Conventions

## Rules

### 1. Language

All commit messages MUST be written in **English**. No exceptions.

### 2. Atomic Commits by Functionality

Each commit should group related changes by functionality, with files organized logically:

```
# GOOD: One commit per feature/fix
feat: add goal creation form
  - app/Http/Controllers/GoalController.php
  - resources/js/Pages/Goals/Create.vue
  - resources/js/Components/GoalForm.vue

# BAD: Mixing unrelated changes
feat: add goal form and fix dashboard chart
  - GoalController.php
  - Create.vue
  - DashboardController.php  <- unrelated
  - Chart.vue                <- unrelated
```

### 3. No AI Attribution

**NEVER** include:
- `Co-Authored-By: Claude`
- `Generated with Claude Code`
- Any reference to AI tools in commit messages

Commits are authored by the developer, period.

### 4. Message Content

Commit messages describe **what was done**, nothing more:

```
# GOOD: Direct and factual
feat: add counter goal type quick log actions
fix: prevent negative values in money goals
refactor: extract progress calculation to trait

# BAD: Verbose or explanatory
feat: add counter goal type quick log actions to allow users to increment and decrement values
fix: prevent negative values in money goals because they don't make sense for savings
refactor: extract progress calculation to trait for better code organization and reusability
```

---

## Commit Message Format

```
<type>: <short description>
```

### Types

| Type       | Description                                      |
|------------|--------------------------------------------------|
| `feat`     | New feature                                      |
| `fix`      | Bug fix                                          |
| `refactor` | Code change that neither fixes a bug nor adds a feature |
| `style`    | Formatting, missing semicolons, etc.             |
| `docs`     | Documentation only                               |
| `test`     | Adding or updating tests                         |
| `chore`    | Build process, dependencies, config              |

### Short Description Rules

- Lowercase, no period at the end
- Imperative mood ("add" not "added" or "adds")
- Max 72 characters
- What, not why or how

---

## Examples

### Feature Commits

```bash
feat: add google oauth login
feat: create goal model and migration
feat: implement quick log counter actions
feat: add year progress bar to dashboard
```

### Fix Commits

```bash
fix: correct percentage calculation for money goals
fix: prevent duplicate user creation on login
fix: handle empty goal list in dashboard
```

### Refactor Commits

```bash
refactor: move goal types to enum
refactor: extract category colors to config
refactor: simplify log entry validation
```

### Chore Commits

```bash
chore: install inertia and vue dependencies
chore: configure tailwind with custom colors
chore: add phpstan to dev dependencies
```

---

## Commit Workflow

### Before Committing

1. Review changes with `git diff`
2. Stage only related files
3. Verify no unrelated changes are included

### Grouping Strategy

```bash
# Working on Quick Log feature? Commit in logical groups:

# Commit 1: Backend
git add app/Http/Controllers/LogEntryController.php
git add app/Http/Requests/StoreLogEntryRequest.php
git add routes/web.php
git commit -m "feat: add log entry controller and routes"

# Commit 2: Frontend components
git add resources/js/Components/QuickLogAction.vue
git add resources/js/Components/CategoryCard.vue
git commit -m "feat: create quick log components"

# Commit 3: Page integration
git add resources/js/Pages/QuickLog/Index.vue
git add resources/js/Pages/QuickLog/Category.vue
git commit -m "feat: add quick log pages"
```

---

## Anti-Patterns

### DO NOT

```bash
# Too vague
git commit -m "update files"
git commit -m "fix bug"
git commit -m "changes"

# Too verbose
git commit -m "feat: add the ability for users to log their progress on counter type goals by clicking buttons"

# Wrong language
git commit -m "añadir formulario de goals"

# AI attribution
git commit -m "feat: add login

Co-Authored-By: Claude <noreply@anthropic.com>"

# Mixed concerns
git commit -m "feat: add login and fix chart and update readme"
```

### DO

```bash
git commit -m "feat: add goal creation form"
git commit -m "fix: correct progress percentage rounding"
git commit -m "chore: update npm dependencies"
```
