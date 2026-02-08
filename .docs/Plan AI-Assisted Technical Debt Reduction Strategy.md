## Plan: AI-Assisted Technical Debt Reduction Strategy

TL;DR: I will help you tackle technical debt through **atomic feature extraction** — dividing the monolithic codebase
into testable, independently-deployable pieces. For each piece, I'll add tests first (safeguard), then fix/refactor (
enhance quality), and document. This approach builds your portfolio while systematically reducing debt.

### Steps

1. **Define Feature Modules** — Map 5–8 core business domains (Payments, Cart/Checkout, Product Catalog, Orders,
   Pricing, Users, Admin, Notifications). Create a [features registry](c:\xampp\htdocs\.z\plan.txt) with business logic
   overview for each.

2. **Assess & Prioritize by Impact** — For each domain, measure: code smell count, test coverage, cyclomatic complexity,
   error handling gaps. Start with high-impact, low-risk modules (e.g., Pricing Service before Payment Gateway).

3. **Establish Test Baseline for Each Feature** — Add unit + feature tests *before* refactoring. Use AI to generate test
   cases based on business rules, then you verify logic. This prevents regression.

4. **Extract & Refactor in Parallel** — For each module: (a) extract globals/helpers into service classes, (b) apply
   SOLID principles, (c) add type hints, (d) reduce PHPStan violations.

5. **Add Automated Error Handling & Logging** — Implement consistent exception hierarchy, add structured logging (JSON),
   create alerting rules for production issues.

6. **Document-as-You-Go** — Generate API docs, architecture diagrams, and decision records (ADRs) for changes. Build
   portfolio-ready documentation.

7. **Set Up CI/CD Gates** — Configure GitHub Actions to enforce test coverage %, PHPStan level 7+, zero critical
   vulnerabilities before merge.

### My Specific Role & Capabilities

**Phase A: Research & Planning** (What I do now)

- Audit code for anti-patterns, coupling, hidden complexity
- Identify extraction boundaries (services, repositories, domain models)
- Map dependencies to predict refactoring impact
- Draft refactoring blueprints with risk analysis

**Phase B: Test Generation & Verification** (Collaborative)

- Generate comprehensive unit test suites for business logic
- You validate test cases match actual business rules
- I generate feature/integration test scenarios
- Output: 80%+ test coverage for each module

**Phase C: Code Transformation** (Your execution, my guidance)

- Provide step-by-step refactoring instructions with code snippets
- Identify all call-sites affected by changes
- Generate service class templates + usage examples
- Flag breaking changes that need data migrations

**Phase D: Documentation & Knowledge Capture** (AI-assisted)

- Auto-generate API docs (OpenAPI/Swagger format)
- Create architecture diagrams (C4 model)
- Generate ADRs explaining design decisions
- Build feature decision tables (business logic → code path)

**Phase E: CI/CD & Quality Gates** (Scaffolding + guidance)

- Generate GitHub Actions workflows for automated checks
- Configure PHPStan, tests, security scanning
- Create pre-commit hooks to catch issues early

### Further Considerations

1. **Sequencing Question**: Start with **Pricing Service extraction** (low risk, high reuse) or **Payment Gateway
   refactoring** (high risk, critical)? Recommendation: Start with Pricing to build confidence, then tackle Payments.

2. **Dependencies**: Should we **freeze sqlupdates/ and consolidate into migrations first** before refactoring domain
   logic? Yes — unstable schema = refactoring risk.

3. **Portfolio Framing**: Document *why* each change (e.g., "Replaced 3,088-line helper into 5 focused services
   following SRP") — this demonstrates architectural thinking for career progression.