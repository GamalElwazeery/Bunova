# Traceability Contract

Every executable canonical task must trace product intent to verifiable evidence.

## Required task trace fields

Each task must expose directly or by referenced authority:

- **ID** — stable phase-scoped identifier.
- **Goal** — concrete outcome, not an activity label.
- **Authority** — product/domain/contract/rule/skill references.
- **Depends** — explicit prerequisite task IDs/gates where material.
- **Scope** — components/surfaces/data affected.
- **Acceptance** — observable completion conditions.
- **Evidence** — tests, screenshots, API examples, migrations/schema inspection, logs, reports, or review evidence as appropriate.

## Bidirectional traceability

Implementation should be traceable back to canonical tasks; major product contracts should be covered by one or more canonical tasks. Orphan code and orphan requirements are planning defects.

## Evidence strength

Evidence must match risk:

- UI polish: screenshots/visual tests + interaction tests where relevant.
- business logic: automated unit/feature/integration tests.
- money/fiscal/stock/time: deterministic tests + invariants + reconciliation evidence.
- external integration: contract tests, idempotency/retry/error fixtures and sandbox/manual evidence.
- security/permissions: negative tests proving denial, not only happy-path success.
- offline/sync: disconnect/reconnect/conflict simulations and convergence checks.

"Implemented" without acceptance evidence is partial, not complete.
