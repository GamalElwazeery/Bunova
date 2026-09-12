# Bunova — ExecutionKit Adoption Baseline

## Identity

- Project: `GamalElwazeery/Bunova`
- Project branch: `main`
- Pre-integration project HEAD: `ac61bdd65ef476d8961ab3534b84d46eed0a7369`
- ExecutionKit source: `Elwazeery/AI-ExcutionKit`
- Source branch: `main`
- Source HEAD inspected for this adoption: `fb49bfa3a16995b4ea785ed6ca66d0da808032c8`
- Published VERSION: `3.0.0`
- Maturity: `M0_PLANNING_ONLY`
- Adoption topology: isolated exact pinned runtime under `.executionkit/runtime/AI-ExecutionKit`
- Product execution state: blocked until `EK00-AUDIT` and `EK00-ACTIVATE` are independently accepted.

## Why preserved topology is required

Bunova is planning-only but is not an empty/unhistoried project. Its native `TODO.md` already contains accepted P00 planning history and a detailed product backlog. The current automatic installer deliberately refuses to infer acceptance semantics from an already-checked native Markdown TODO. Rewriting accepted P00 history or resetting checkboxes merely to make `install --auto` pass would violate both Bunova governance and ExecutionKit preservation rules.

The current ExecutionKit Playbook explicitly permits an isolated exact pinned runtime for mature native task projections/semantics. Bunova therefore uses that supported preservation path instead of installing a second managed runtime or discarding native history. Runtime/tool invocations must target the pinned path, and project wrappers/host integration must prove path parity before activation.

## Native authority preserved

- `TODO.md` remains the sole mutable backlog/status authority.
- `AGENTS.md` remains Bunova's root project instruction authority and will be merged with ExecutionKit startup/routing rules rather than replaced.
- `docs/00-product/` through `docs/05-governance/` remain Bunova product/planning authorities.
- `contracts/`, `rules/`, `skills/`, `agents/`, and `workflows/` are project-native resources to register and route through Agent OS.
- P00 accepted planning history is preserved as accepted history; product implementation remains untouched.

## Native lifecycle normalization

ExecutionKit native-Markdown semantics are canonical for active checkbox tasks:

- `[ ]` = PLANNED
- `[~]` = IMPLEMENTED
- `[R]` = REVIEW_REQUIRED
- `[!]` = CORRECTION_REQUIRED
- `[B]` = BLOCKED
- `[x]` = ACCEPTED

Bunova's earlier draft documentation used `[!]` for blocked. No current Bunova task is using that marker as an active blocked record, so the documentation can be normalized safely before execution. Future blockers use `[B]`.

The EK00 integration chain itself must be represented as canonical ExecutionKit task blocks so integration tasks are not accidentally gated by their own activation dependency. Native P01+ checkbox work remains dependency-gated by the accepted activation boundary.

## Eight-system applicability

| System | Bunova policy |
| --- | --- |
| Execution OS | FULL; preserve native TODO and phase/task detail. |
| Agent OS | FULL; register and route both managed ExecutionKit resources and Bunova specialists. |
| Content OS | FULL; Arabic/English product copy, help, errors, receipts, fiscal/customer content, media and localization. |
| SEO OS | STANDARD; applies to Bunova public commercial/acquisition website only. POS/Admin/Platform Admin/authenticated operational surfaces are non-indexable/private. Menuza remains a separate product boundary. |
| Audit OS | FULL; financial/offline/fiscal/security/premium UX/domain audits and independent phase gates. |
| Test OS | FULL; M0 currently has no product runtime tests, so runtime test adequacy transitions on when P01 creates real Laravel/Flutter code. |
| Launch OS | FULL; exact candidate, backups/restore, staged rollout, pilots, ETA/payment/hardware/runtime evidence. |
| State OS | REQUIRED cross-cutting; repository truth wins, memory advisory only, zero-context Amnesia Test required before activation. |

## Premium Experience

Bunova is UI-bearing across Owner/Admin Web, Platform Admin, POS, waiter/handheld, bar/KDS, gaming supervisor, customer display and public commercial web. Premium Experience is `REQUIRED`, quality profile `premium`. Existing Bunova design/UX authorities are preservation inputs; the ExecutionKit quality contract must be reconciled to them, not replace them with a generic starter.

## Public/search boundary

Organic search is intended for Bunova's public commercial/acquisition website. Initial SEO profile is `STANDARD`. Search contracts must cover the real public page families once routes/content exist. Authenticated/private operational applications must fail closed against indexing. Menuza's public-menu/search behavior remains owned by Menuza and its integration contract, not silently absorbed into Bunova SEO scope.

## Tooling/host/CI evidence boundary

Repository files do not prove local host activation. The following remain `NOT_RUN` until direct environment evidence exists:

- submodule initialization on an authenticated workstation;
- Node/runtime command execution against the pinned source;
- Antigravity host integration and MCP handshake/tool calls;
- Memory MCP availability;
- macOS/Windows self-hosted runner registration;
- Laravel Boost / Dart MCP applicability before corresponding runtimes exist;
- physical printer/router/payment/device evidence.

Missing optional fleet/control-plane connectivity is `NOT_CONFIGURED`, not a canonical execution blocker.

## Activation conditions

Before product work may start, the canonical EK00 chain must prove: runtime identity; tooling bootstrap; project study; Execution/Agent/State/Content/SEO/Premium/Audit/Test/Launch reconciliation; MCP/tool policy; manual self-hosted CI policy; discovery/scope reconciliation; independent integration audit; and activation. `projectState` remains `BOOTSTRAP` until the activation operation and its independent acceptance are complete.
