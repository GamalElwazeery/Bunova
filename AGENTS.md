# Bunova Agent Operating Authority

This file is mandatory reading for every human or AI agent working in this repository.

## 1. Read order before any work

1. Inspect the live repository state: branch, HEAD, status/equivalent, recent commits, open work, and existing implementation.
2. Read `TODO.md` completely enough to understand the current phase, dependencies, task semantics, and evidence expectations.
3. Read the authorities referenced by the selected task under `docs/`, `contracts/`, `rules/`, `skills/`, and `workflows/`.
4. Inspect existing code/evidence before changing anything. Never treat a checkpoint, prompt, or prior conversation as newer than repository truth.

## 2. Canonical backlog rule

`TODO.md` is the sole mutable execution/backlog authority.

- Do not create shadow TODOs, hidden backlogs, phase checklists, issue lists, or private execution queues.
- Planning documents describe product truth and constraints; they do not become alternate mutable backlogs.
- If implementation discovers missing work, add or refine the canonical task in `TODO.md` with traceability before executing it.

## 3. Task selection

Execute only the earliest dependency-ready eligible canonical task unless `TODO.md` explicitly defines a different selection rule.

Never skip unfinished dependencies merely because a later task is easier. If objectively blocked, record the exact blocker and continue only with tasks whose dependency graph does not rely on it.

## 4. Task depth

A task is executable only when it states or references enough truth to determine:

- goal and business behavior;
- affected domain/surfaces;
- authoritative plans/contracts/rules;
- dependencies and invariants;
- implementation expectations;
- negative/error/offline/permission states where applicable;
- tests/evidence required;
- acceptance conditions.

Do not invent missing product behavior silently. Resolve ambiguity against canonical authorities and update planning/task truth when necessary.

## 5. Completion semantics

Do not mark a task complete because code exists.

Completion requires the task's acceptance criteria, tests, evidence, documentation synchronization, and relevant audit checks. Partial implementation remains partial.

Every completed task must leave:

- implementation/evidence consistent with the task;
- no known hidden placeholders or demo-only shortcuts;
- `TODO.md` updated immediately;
- architecture/contracts/docs updated if the task changed canonical behavior.

## 6. Bunova-specific architectural rules

- Capabilities are architectural; café presets are onboarding configuration only.
- Bunova is the café operational system of record.
- Menuza owns customer-facing menu/QR/public ordering experience; Bunova owns operational acceptance, fulfillment, payment, stock, and fiscal truth.
- Restaurant-system reuse must happen through explicit contracts/shared extraction, never blind copy/paste.
- Orders, bills, payments, fiscal records, stock movements, session timing, and audit trails must preserve historical integrity.
- Product sales, timed services, Wi-Fi access, gaming, rooms, shisha, and other billable concepts must converge through the canonical billing model rather than parallel incompatible checkout engines.
- Operational apps are offline-aware by design. Network loss must be explicitly modeled, tested, and recoverable.

## 7. UX rule

"Premium" means operationally excellent, not visually noisy. Every production surface must cover loading, empty, error, offline, denied, partial, conflict, success, and destructive states where relevant; be responsive; support RTL Arabic and English; be touch/keyboard accessible for its device context; and minimize steps on high-frequency workflows.

No generic admin-template CRUD is accepted for operationally important flows.

## 8. Testing rule

Use focused tests during a task where risk requires them. Run the phase/wave verification suite at the phase gate, not mechanically after every tiny task.

Financial, fiscal, sync, permissions, offline conflict, session billing, stock, and integration contracts require stronger test evidence than ordinary presentation work.

## 9. Data and security

- Tenant/organization/branch boundaries are explicit and tested.
- Authorization is server-enforced; hidden UI is not authorization.
- Secrets never enter source control.
- Sensitive mutations require auditability.
- Destructive actions require explicit permission and appropriate confirmation/reason semantics.

## 10. Planning-only boundary for current repository state

Until `TODO.md` closes the planning/readiness frontier and explicitly opens implementation, do not scaffold the application or implement product code. The AI-ExecutionKit integration is also deferred to the next dedicated round; do not install it opportunistically during native planning work.
