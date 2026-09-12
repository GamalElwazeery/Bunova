# Bunova Agent Operating Authority

This file is mandatory reading for every human or AI agent working in this repository.

## 1. Startup order before any work

1. Inspect live repository truth: branch, HEAD, working tree/equivalent, recent commits, interrupted work and current implementation/evidence. Repository truth beats prompts, memories and checkpoints.
2. Read `execution.config.json`, `docs/executionkit/ADOPTION.md`, this file and the current ExecutionKit pin/version when K00 or runtime behavior is relevant.
3. Read `TODO.md` enough to understand current phase, statuses, dependencies, task detail and gates. `TODO.md` is the sole mutable backlog authority.
4. Resolve the current task through the generated hash-bound ExecutionKit projection and `.agents/manifest.json`; the projection/manifest are routing views, never task-state owners.
5. Read the task-linked Bunova authorities under `docs/`, `contracts/`, `rules/`, `skills/`, `agents/` and `workflows/` before mutation.
6. Inspect existing code/evidence before changing anything. Never reimplement existing or interrupted work from scratch without proving it is invalid.

## 2. ExecutionKit adoption boundary

The exact runtime source is pinned at `.executionkit/runtime/AI-ExecutionKit`. Do not edit upstream runtime files from Bunova. Project-specific reconciliation belongs in root/project files.

Bunova preserves its native TODO semantics using `scripts/executionkit/project-tasks.mjs` with `projection-json`; `.executionkit/task-projection.json` is derived/hash-bound and never mutable authority. Existing Bunova `[!]` means BLOCKED; adoption does not rewrite historical task bytes merely to mimic generic marker syntax.

K00 is mandatory. `K00-019` is the independent deep-integration audit boundary and `K00-GATE` is activation. P01 product implementation is forbidden until K00 activation is accepted and current validator/host evidence supports it.

## 3. Canonical backlog and task selection

`TODO.md` is the sole mutable execution/backlog authority. Do not create shadow TODOs, hidden backlogs, issue queues, private execution lists, state-owned tasks or checklist mirrors that can drift.

Execute one lifecycle action at a time on the earliest dependency-ready eligible task. A `/goal` host loop may persist the session but never chooses work. If objectively blocked, record the exact blocker in canonical state and proceed only to dependency-independent eligible work.

After every accepted task transition, update `TODO.md` immediately, synchronize stable authorities if truth changed, checkpoint State OS and resolve the next action afresh.

## 4. Task depth and traceability

A task is executable only when it states or links enough truth for business behavior, affected domains/surfaces, plans/contracts/rules, dependencies/invariants, negative/error/offline/permission states, verification/evidence and acceptance. Do not silently invent missing product behavior. New discovered work is added to canonical TODO before execution.

## 5. Completion and review

Code existence is not completion. Acceptance requires criteria, focused tests/evidence, synchronized docs/contracts and required review. Implementation and independent review must remain distinguishable; Audit OS reports evidence/findings and findings return to TODO.

Heavy/full CI is phase-boundary by default. Run targeted checks during tasks according to risk. Never report unavailable runtime, MCP, physical-device, provider, self-hosted-runner or production-search evidence as PASS.

## 6. Bunova domain invariants

- Capabilities are architecture; café archetype presets are onboarding configuration only.
- Bunova owns café operational truth; Bunova Cloud commercial subscription truth is a separate platform bounded context.
- Menuza owns customer-facing menu/QR/public ordering experience; Bunova owns operational acceptance, fulfillment, billing/payment, stock and fiscal truth.
- Restaurant reuse is explicit contract/shared extraction, never blind copy/paste.
- Product sales, timed services, gaming, Wi-Fi, rooms, shisha and other billables converge through canonical Billing; no parallel checkout engine.
- Financial, fiscal, stock, time/session, stored-value, house-account, supplier/payroll-lite and audit history uses compensating/history-preserving semantics rather than destructive edits.
- Operational apps are offline-aware; network loss, replay, collision, stale config and recovery are explicit and tested.
- Commercial entitlement, enabled capability and user/device authorization are distinct gates.

## 7. Premium Experience / Content / SEO

Premium Experience is REQUIRED and governed by `executionkit.quality.json` plus native UX/design authorities. Extend canonical components before one-offs; use production-shaped data; cover required states/form factors/RTL-LTR/input modes; inspect rendered output; reject generic CRUD/AI-template UX.

Content OS is FULL for Arabic/English operational, fiscal, help, notification and commercial content. No lorem, fabricated claims/metrics/testimonials or hidden placeholder copy.

SEO OS STANDARD applies only to Bunova-owned public commercial/acquisition surfaces declared in `executionkit.seo.json`. POS/Admin/Platform Admin/authenticated operational surfaces remain private/non-indexable. Menuza SEO remains Menuza-owned.

## 8. State OS

State OS is derived continuity, not a task system. Checkpoint after canonical lifecycle transitions and meaningful handoffs; rehydrate on HEAD/TODO/config drift. Memory is advisory only and may not own task state. K00 activation requires a real zero-context Amnesia Test on a runtime-capable host.

## 9. Security, data and evidence

Tenant/organization/branch boundaries are explicit and tested. Platform operator authority is separately protected. Server authorization is canonical; hidden UI is not authorization. Secrets never enter source. Sensitive actions are auditable. Destructive/override actions require explicit permission and appropriate confirmation/reason.

Evidence must name what actually ran, environment, candidate/source identity and artifact where required. Presence of config files is not proof that tools, MCPs, runners, physical devices/providers or production systems work.

## 10. Current frontier

Native P00 planning is accepted. Current frontier is K00 deep ExecutionKit integration only. Do not scaffold Laravel/Flutter product code until `K00-GATE` is accepted and project state is legitimately activated.
