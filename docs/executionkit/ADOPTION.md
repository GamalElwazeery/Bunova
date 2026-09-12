# Bunova — ExecutionKit Adoption Baseline

## Identity

- Project: `GamalElwazeery/Bunova`
- Project branch: `main`
- Pre-integration project HEAD: `ac61bdd65ef476d8961ab3534b84d46eed0a7369`
- ExecutionKit source: `Elwazeery/AI-ExcutionKit`
- Source branch: `main`
- Source HEAD inspected/pinned: `fb49bfa3a16995b4ea785ed6ca66d0da808032c8`
- Published VERSION: `3.0.0`
- Maturity: `M0_PLANNING_ONLY`
- Topology: exact pinned isolated runtime at the canonical installed path `.executionkit/runtime/kit`
- Product execution: blocked until the K00 audit/activation boundary is accepted.

## Why preserved topology is required

Bunova is planning-only but not unhistoried: its native `TODO.md` already contains accepted P00 planning history plus a detailed product backlog. The current automatic installer correctly refuses to infer acceptance semantics from pre-checked native Markdown and also instructs mature native projections/pinned runtimes to use the preserved-topology procedure. Rewriting P00 acceptance merely to make `install --auto` pass would violate preservation rules.

Bunova therefore uses the supported preservation path: one exact pinned runtime, one explicit native task projection and project-local reconciliation. It does not install a second managed source tree, replace accepted history or create another backlog.

## Native task authority and projection

`TODO.md` remains the only mutable backlog/status owner. `scripts/executionkit/project-tasks.mjs` regenerates `.executionkit/task-projection.json` whenever ExecutionKit loads tasks. The projection declares hashes for `TODO.md` and `TODO_ARCHIVE.md`; stale source hashes invalidate it. The projector reads canonical state but never rewrites the source files.

Bunova's established source-marker meanings are preserved rather than rewritten. For machine lifecycle routing, `[ ]` projects to `READY` (not yet implemented, dependency-gated and therefore executable when eligible), `[~]` to `IMPLEMENTED`, `[!]`/`[B]` to `BLOCKED`, `[R]` to `REVIEW_REQUIRED`, and `[x]` to `ACCEPTED`. This translation is deliberate: the pinned ExecutionKit executor implements `READY`, reviews `IMPLEMENTED`/`REVIEW_REQUIRED`, and requires accepted dependencies before advancing. The source checkbox itself remains unchanged until canonical task state is explicitly updated.

The existing K00 vocabulary is reconciled as follows: `K00-001` is tooling-bootstrap/intake; `K00-002` is project-study; `K00-005` carries Premium Experience applicability/construction reconciliation; `K00-009` is SEO applicability; `K00-019` is `Action: audit` with phase boundary; `K00-GATE` is activation. For P01+, the first normal task waits on the previous phase gate, subsequent normal tasks are sequential, the phase audit waits on all normal tasks and the phase gate waits on the audit. P01 therefore cannot open before `K00-GATE`.

## Native authority preserved

Root `AGENTS.md`, native product/architecture/domain/experience/delivery/governance docs, contracts, rules, skills, agents and workflows remain Bunova product truth. `.agents/manifest.json` registers/routes them instead of replacing them with generic resources. K00 uses a dedicated ExecutionKit integrator route; the independent audit route remains separate and higher-priority for audit lifecycle actions.

## Eight-system applicability

Execution OS FULL; Agent OS FULL; Content OS FULL; SEO OS STANDARD; Audit OS FULL; Test OS FULL; Launch OS FULL; State OS required cross-cutting. Premium Experience is REQUIRED for all UI-bearing work and uses quality schema 1.1.

SEO scope is only Bunova-owned public commercial/acquisition pages. Authenticated operational applications are private. Menuza public-menu/search behavior remains outside Bunova ownership.

## Tooling/host/CI evidence boundary

Repository files alone do not prove submodule initialization, Node validator execution, Antigravity integration, MCP handshake/tool calls, Memory MCP availability, macOS/Windows self-hosted runner registration, future Laravel Boost/Dart MCP applicability or physical printer/router/payment/device behavior. Until direct current evidence exists these remain NOT_RUN/NOT_CONFIGURED/NOT_APPLICABLE as appropriate, never PASS.

The repository-level static reconciliation has verified the canonical runtime path and required v3 project-surface schemas/docs against the pinned source, but executable project validators remain direct runtime evidence and cannot be inferred from static presence. Missing optional fleet/control-plane connectivity is degraded observability, not canonical execution failure.

## Activation conditions

K00 must prove exact runtime identity; project study; Execution/Agent/State/Content/SEO/Premium/Audit/Test/Launch reconciliation; routing; task-projection parity; MCP/tool policy; manual self-hosted CI policy; discovery/scope reconciliation; independent integration audit; checkpoint/rehydration/Amnesia evidence and activation. `projectState` remains `BOOTSTRAP` until then.
