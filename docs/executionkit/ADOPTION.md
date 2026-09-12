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
- Topology: exact pinned isolated runtime under `.executionkit/runtime/AI-ExecutionKit`
- Product execution: blocked until the K00 audit/activation boundary is accepted.

## Why preserved topology is required

Bunova is planning-only but not unhistoried: its native `TODO.md` already contains accepted P00 planning history plus a detailed product backlog. The current automatic installer correctly refuses to infer acceptance semantics from pre-checked native Markdown. Rewriting P00 acceptance merely to make `install --auto` pass would violate preservation rules.

The current Playbook supports isolated pinned runtime + explicit native task projection when mature native semantics must be retained. Bunova therefore uses that supported preservation path, not a partial copied runtime and not a second backlog.

## Native task authority and projection

`TODO.md` remains the only mutable backlog/status owner. `scripts/executionkit/project-tasks.mjs` regenerates `.executionkit/task-projection.json` whenever ExecutionKit loads tasks. The projection declares hashes for `TODO.md` and `TODO_ARCHIVE.md`; stale source hashes invalidate it.

Bunova's established marker meanings are preserved rather than rewritten: `[ ]` PLANNED, `[~]` IMPLEMENTED, `[!]` BLOCKED, `[x]` ACCEPTED. The projector also understands `[R]` REVIEW_REQUIRED and `[B]` BLOCKED if introduced later. Derived task metadata supplies machine dependencies/tags/audit boundaries without owning status.

The existing K00 vocabulary is reconciled as follows: `K00-001` is tooling-bootstrap/intake; `K00-002` is project-study; `K00-005` carries Premium Experience applicability/construction reconciliation; `K00-009` is SEO applicability; `K00-019` is `Action: audit` with phase boundary; `K00-GATE` is activation. P01 and every later product phase are deterministically dependency-gated behind K00 and the previous phase gate.

## Native authority preserved

Root `AGENTS.md`, native product/architecture/domain/experience/delivery/governance docs, contracts, rules, skills, agents and workflows remain Bunova product truth. `.agents/manifest.json` registers/routes them instead of replacing them with generic resources.

## Eight-system applicability

Execution OS FULL; Agent OS FULL; Content OS FULL; SEO OS STANDARD; Audit OS FULL; Test OS FULL; Launch OS FULL; State OS required cross-cutting. Premium Experience is REQUIRED for all UI-bearing work and uses quality schema 1.1.

SEO scope is only Bunova-owned public commercial/acquisition pages. Authenticated operational applications are private. Menuza public-menu/search behavior remains outside Bunova ownership.

## Tooling/host/CI evidence boundary

Repository files alone do not prove submodule initialization, Node validator execution, Antigravity integration, MCP handshake/tool calls, Memory MCP availability, macOS/Windows self-hosted runner registration, future Laravel Boost/Dart MCP applicability or physical printer/router/payment/device behavior. Until direct current evidence exists these remain NOT_RUN/NOT_CONFIGURED/NOT_APPLICABLE as appropriate, never PASS.

Missing optional fleet/control-plane connectivity is degraded observability, not canonical execution failure.

## Activation conditions

K00 must prove exact runtime identity; project study; Execution/Agent/State/Content/SEO/Premium/Audit/Test/Launch reconciliation; routing; task-projection parity; MCP/tool policy; manual self-hosted CI policy; discovery/scope reconciliation; independent integration audit; checkpoint/rehydration/Amnesia evidence and activation. `projectState` remains `BOOTSTRAP` until then.
