---
title: "MCP Agents Guide: Laravel & PHPStan Rules"
source: "robmellett.com"
url: "https://robmellett.com/articles/laravel-phpstan-agent"
date_saved: "2026-01-21T23:55:36.395Z"
date_published: "2025-10-09"
word_count: "862"
reading_time: "5 min"
description: "A field manual for autonomous or human agents working in PHP repos using PHPStan"
---

## MCP Agents Guide: PHPStan Rules

> A field manual for autonomous or human agents working in PHP repos using PHPStan. Copy this into your project’s `docs/agents/phpstan.md` and adapt as needed.

---

## 1) Mission & Scope

-   **Mission:** Keep code safe, boring, and predictable by enforcing static analysis with **PHPStan** at a consistent level across services.
-   **Scope:** All PHP source, tests, and frameworks (Laravel/Symfony/etc.) with framework-specific add‑ons (e.g., Larastan).
-   **Success Criteria:**
    1.  No increase in PHPStan baseline size.
    2.  New/changed lines meet target level.
    3.  Deprecated/unsafe APIs trend to zero.

---

## 2) Defaults & Conventions

-   **Minimum level:**`max` for new greenfield packages, `7–8` for legacy apps (ratchet up by +1 each sprint).
-   **Bleeding edge:** Enabled in CI for canary runs to surface upcoming breakages; optional locally.
-   **Paths:** Analyze `app/`, `src/`, `domain/`, and `tests/` (unit + feature). Exclude generated code.
-   **Cache:** Use PHPStan cache for speed; never commit cache.
-   **Parallel:** Prefer parallel runs in CI (speed + determinism).

---

## 3) How to Run

```bash
# local dev (fast)
vendor/bin/phpstan analyse -l max -c phpstan.neon --memory-limit=1G

# with baseline respected
vendor/bin/phpstan analyse -c phpstan.neon --generate-baseline=phpstan-baseline.neon

# CI strict (fail on new errors)
vendor/bin/phpstan analyse -c phpstan.neon --error-format=github
```

---

## 4) Config Structure (example)

```
# phpstan.neon
parameters:
  level: 8
  paths:
    - app
    - src
    - domain
    - tests
  excludePaths:
    - */storage/*
    - */bootstrap/*
    - */vendor/*
    - */generated/*
  checkGenericClassInNonGenericObjectType: true
  checkMissingIterableValueType: true
  checkMissingCallableSignature: true
  checkTooWideReturnTypesInProtectedAndPublic: true
  checkUninitializedProperties: true
  reportUnmatchedIgnoredErrors: true
  treatPhpDocTypesAsCertain: false
  universalObjectCratesClasses:
    - stdClass
  autoload_files:
    - %rootDir%/../../../bootstrap/autoload.php
  ignoreErrors:
    # Always comment *why* and a tracking ticket
    - message: "#Call to an undefined method .*->whereJsonContains\(#"
      paths: [tests/*]
      count: 5
includes:
  - phpstan-baseline.neon

# For Laravel projects
includes:
  - vendor/nunomaduro/larastan/extension.neon
```

---

## 5) Rule Categories & Expectations

### 5.1 Types & Signatures

-   **Return types & param types must be declared** wherever possible.
-   **Generics**: Prefer concrete type parameters for collections (e.g., `Collection<User>`). Avoid `mixed`.
-   **Nullability**: Avoid nullable where business rules forbid it; use Value Objects instead of `?string`.
-   **Callable shapes** must be specified; don’t use untyped callbacks.

### 5.2 Properties & Initialization

-   All **readonly** or **private** by default; initialize in constructor/factory.
-   Enable `checkUninitializedProperties` to catch missed assignments.

### 5.3 Exceptions & Control Flow

-   **Throw precise exceptions**; document with `@throws` and keep catch blocks specific.
-   Avoid using exceptions for normal control flow. Prefer Result/Either objects where suited.

### 5.4 Arrays vs Objects

-   Replace associative arrays with **DTOs/Value Objects**. If arrays are unavoidable, document shapes with `array{...}`.

### 5.5 Inheritance & Visibility

-   Prefer **composition** over inheritance. Avoid widening visibility or return types in children.
-   Mark internal methods with `@internal` and keep them small and testable.

### 5.6 Deprecations & Unsafe APIs

-   Forbid new uses of **deprecated** functions/classes; allow only in migration shims.
-   Ban dynamic property creation; use `__set_state` or explicit setters when necessary.

### 5.7 Framework‑specific (Laravel/Symfony)

-   Validate container bindings and facades via framework extensions (Larastan/Symfony plugin).
-   For Eloquent, specify relationship generics: `HasMany<Order>` etc.

---

## 6) Error Triage Workflow (Agent SOP)

1.  **Reproduce** locally with the same flags as CI.

2.  **Classify** the error:

    -   Bug (real type/logic issue)
    -   Design smell (over‑broad types, hidden nulls)
    -   Tooling false positive (rare; prefer code changes first)
3.  **Fix** in priority order:

    1.  add/strengthen types; 2) refactor to DTO/VO; 3) tighten generics; 4) adjust phpdoc; 5) last resort ignore.
4.  **Test**: add/adjust unit + feature tests proving the contract.

5.  **Document**: if ignoring, add a comment with a **Jira/Ticket** and a removal date.


---

## 7) Suppression Policy

-   **Defaults:**`ignoreErrors` allowed only with **message regex + path + count** and a **commented reason**.
-   **Bans:** global `ignoreErrors` without path scoping; `treatPhpDocTypesAsCertain: true` in legacy code.
-   **Expiry:** Each ignore carries a ticket; review weekly. CI should **fail** if `reportUnmatchedIgnoredErrors` triggers.

---

## 8) Baseline Policy

-   Maintain `phpstan-baseline.neon` for legacy debt only.
-   **No growth rule:** CI blocks PRs that increase baseline count.
-   **Shrink rule:** When fixing code, regenerate to remove resolved entries.
-   Regenerate with:

```bash
vendor/bin/phpstan --configuration=phpstan.neon --generate-baseline
```

---

## 9) CI Gate (template)

```yaml
# .github/workflows/phpstan.yml
name: PHPStan
on: [pull_request]
jobs:
  analyse:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          coverage: none
          extensions: mbstring, intl, json
      - run: composer install --no-progress --prefer-dist
      - run: vendor/bin/phpstan analyse -c phpstan.neon --error-format=github
```

---

## 10) Laravel (Larastan) Add‑Ons

```
includes:
  - vendor/nunomaduro/larastan/extension.neon
parameters:
  larastan:
    container_path: bootstrap/app.php
    model_properties_scan_depth: 2
    concise: true
  checkModelProperties: true
  checkMissingMorphToParameters: true
  checkPhpDocMissingReturnType: true
```

**Rules of thumb:**

-   Type Eloquent relations with generics (e.g., `HasMany<Invoice>`), repositories return DTOs not arrays, and request validation creates **typed** DTOs.

---

## 11) Common Pitfalls & Fix Patterns

-   **`mixed` everywhere →** introduce interfaces + generics.
-   **Array payloads →** shape types or DTO classes.
-   **Untyped factories →** static constructors returning concrete types.
-   **Facade/static calls** in Laravel → delegate into typed services.
-   **Magic dynamic properties →** define real properties, enable strict constructors.

---

## 12) Agent Playbooks

-   **New module:**

    1.  Start at level `max` with zero baseline.
    2.  Add types, DTOs, and Result objects from first commit.
-   **Legacy increment:**

    -   Raise level one notch after baseline shrinks by ≥15%.
-   **PR checklist:**

    -   Types on all new/changed public APIs.
    -   No new deprecations, no baseline growth.
    -   Tests cover typed contracts.

---

## 13) FAQ (Fast Answers)

-   **Why did PHPStan flag my array?** It can’t infer shape; convert to DTO or add `array{...}`.
-   **Can I ignore false positives?** Only after exhausting code fixes, with scoped `ignoreErrors` + ticket.
-   **What level should we use?** Max for new libs; otherwise 7–8 and climb.
-   **How do we handle framework magic?** Use official extensions (Larastan/Symfony) and add missing types explicitly.

---

## 14) Glossary

-   **DTO**: Data Transfer Object—typed structure instead of arrays.
-   **VO**: Value Object—immutable, validated type.
-   **Baseline**: Snapshot of known issues; must shrink over time.
-   **Suppression**: Scoped, justified `ignoreErrors` entry with expiry.

---

## 15) Keep it Green

-   Treat new PHPStan errors as **regressions**.
-   Prefer refactoring code over tweaking config.
-   Small, steady reductions beat big bang rewrites.

MCP Agents Guide: Laravel & PHPStan Rules - Rob Mellett