# Status Semantics and Definition of Done

## Canonical task markers

- `[ ]` — not started / no accepted implementation evidence.
- `[~]` — implementation exists or work started, but acceptance is incomplete.
- `[!]` — objectively blocked; blocker must be written beside the task with evidence and dependency impact.
- `[x]` — fully complete under this Definition of Done.

Do not invent additional markers without updating this authority.

## Definition of Done

A task may be `[x]` only when all applicable conditions hold:

1. Required behavior is implemented without knowingly substituting a mock/placeholder/demo shortcut.
2. Acceptance criteria are demonstrably satisfied.
3. Focused automated tests appropriate to the risk pass.
4. Negative, permission, error and offline states are covered where applicable.
5. Financial/fiscal/time/stock invariants reconcile where applicable.
6. User-facing work meets Bunova UX/state/RTL/accessibility rules.
7. Observability/audit hooks exist for sensitive or operationally important behavior.
8. Documentation/contracts/schema examples are synchronized.
9. No newly introduced lint/static-analysis/test failure remains.
10. Evidence is captured or reproducible from repository commands/tests.
11. `TODO.md` is updated immediately after completion.

## Phase done

A phase closes only after all tasks are `[x]`, phase verification passes, audits are resolved, cross-phase regressions are checked, and the phase exit gate is explicitly satisfied.
