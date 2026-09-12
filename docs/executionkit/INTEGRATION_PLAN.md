# Bunova ExecutionKit Deep Integration Plan

This document is stable integration authority, not a backlog. Mutable task state remains only in `TODO.md`.

## Runtime topology

Bunova pins the exact current ExecutionKit source as `.executionkit/runtime/kit` using a Git submodule. This is both the canonical v3 installed runtime path and the preservation topology selected because Bunova had accepted native P00 history before adoption. Project-local commands target that exact pin; the upstream runtime is not edited from Bunova.

## Native task adapter

`TODO.md` remains canonical for mutable task state. `scripts/executionkit/project-tasks.mjs` regenerates `.executionkit/task-projection.json`; the projection is hash-bound to `TODO.md` and `TODO_ARCHIVE.md`, and the adapter reads but never rewrites canonical task state.

Bunova native markers remain authoritative: `[ ]` PLANNED, `[~]` IMPLEMENTED, `[!]` BLOCKED, `[x]` ACCEPTED. Optional `[R]` and `[B]` are understood for compatibility, but adoption does not rewrite historical semantics. The projection adds deterministic dependency/routing metadata without owning mutable state.

For the pre-existing K00 task vocabulary, `K00-019` is the independent phase audit boundary and `K00-GATE` is the activation boundary. K00 preserves its explicit dependency chain. For P01+, the first normal task waits on the previous phase gate, each subsequent normal task waits on its predecessor, the phase audit waits on all normal tasks, and the phase gate waits on the audit. P01 therefore starts only after `K00-GATE`.

## K00 task reconciliation

- `K00-001`: inspect exact live kit source and tooling surface; tagged `tooling-bootstrap`.
- `K00-002`: inspect current Bunova truth and preservation boundaries; tagged `project-study`.
- `K00-003`: establish the supported pinned runtime/adoption topology and deterministic project config.
- `K00-004`: merge root operator/agent instructions and project-study conclusions without weakening Bunova authority.
- `K00-005`: integrate all eight systems and Premium Experience construction invariant; presence alone is insufficient.
- `K00-006`: Execution OS / task projection / lifecycle / dependency / evidence semantics.
- `K00-007`: Agent OS manifest, deterministic routes, reviewer separation and native specialist reachability.
- `K00-008`: Content OS for Arabic/English operational, fiscal, help and public-commercial content.
- `K00-009`: SEO OS STANDARD for Bunova public commercial acquisition surfaces only; authenticated apps are non-indexable.
- `K00-010`: Audit OS independent evidence-first boundaries, including anti-generic/premium/domain/financial/offline checks.
- `K00-011`: Test OS targeted task verification + phase-boundary full self-hosted CI.
- `K00-012`: Launch OS exact-candidate, migration, backup/restore, pilot, provider/device and rollback evidence.
- `K00-013`: State OS rehydrate/checkpoint/amnesia behavior; State never owns task state.
- `K00-014`: register and route all Bunova-native agents/skills/rules/workflows/contracts, including Platform SaaS specialists.
- `K00-015`: MCP/tool/host/framework integration, with direct evidence or truthful `NOT_RUN`/`NOT_APPLICABLE`.
- `K00-016`: prove projection parity and single `TODO.md` authority; no shadow queue.
- `K00-017`: run all applicable validators/readiness checks and repair Bunova-specific conflicts.
- `K00-018`: operator docs/start-resume contract and exact pinned commands.
- `K00-019`: independent phase audit; `Action: audit`, boundary `phase` in the derived projection.
- `K00-GATE`: activation boundary. Product implementation remains forbidden until audit acceptance and activation evidence are valid.

## Agent routing boundary

All K00 implementation/reconciliation work routes through `role.executionkit-integrator` and `skill.executionkit-integration`. Audit lifecycle actions retain higher priority and route to `role.qa-auditor`, so `K00-019` cannot be self-accepted by the integration specialist. Domain-specific supplements may add SEO, Premium, Content, State or Launch context without replacing the K00 primary specialist.

## Eight systems

Execution, Agent, Content, Audit, Test and Launch are FULL. SEO is STANDARD and bounded to Bunova-owned public commercial pages. State OS is cross-cutting and required. Premium Experience is required across all UI-bearing web/Flutter/desktop surfaces.

## Evidence boundary

Repository configuration is not proof of local runtime, MCP, editor, physical device, self-hosted runner, payment terminal, printer, router, ETA sandbox or app-store behavior. Those claims require direct current evidence. Static reconciliation may prove paths/config/contracts, but executable validators remain `NOT_RUN` until actually executed. Missing optional fleet/control-plane connectivity is degradation, not canonical execution failure.
