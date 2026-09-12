# Bunova ExecutionKit Host Runbook

Run from Bunova repository root on an authenticated workstation. The runtime is the exact submodule pin; do not run an arbitrary global kit checkout for acceptance evidence.

## Owner / Antigravity entry contract

The owner is **not** expected to assemble or execute this command list manually. After pulling the current `main`, the Antigravity agent must read `AGENTS.md` and canonical `TODO.md`, inspect live repository truth, and own the K00 host-acceptance sequence below. These commands are the exact diagnostic/acceptance operations the agent should execute and record while reconciling the earliest dependency-ready K00 task.

If Antigravity needs an editor restart to discover the newly pulled workspace plugin/hooks/MCP configuration, the agent should stop at that concrete host boundary and request the restart. After restart, normal entry is the canonical `TODO.md`; the owner should not need to repeat bootstrap commands. Repository configuration or a restart request is not itself acceptance evidence.

## 1. Materialize exact runtime

```bash
git submodule sync --recursive
git submodule update --init --recursive
git -C .executionkit/runtime/kit rev-parse HEAD
cat .executionkit/runtime/kit/VERSION
```

Expected runtime identity for this K00 baseline: `fb49bfa3a16995b4ea785ed6ca66d0da808032c8`, published VERSION `3.0.0`.

The agent must compare the materialized identity with both the Bunova gitlink and current live upstream truth before accepting it. Do not silently advance the pin during validation.

## 2. Generate canonical derived task projection

```bash
node scripts/executionkit/project-tasks.mjs
```

Do not edit `.executionkit/task-projection.json`; it is regenerated and hash-bound to `TODO.md` / `TODO_ARCHIVE.md`. Native `[ ]` tasks project to `READY`, `[~]` to `IMPLEMENTED`, `[R]` to `REVIEW_REQUIRED`, `[!]`/`[B]` to `BLOCKED` and `[x]` to `ACCEPTED`; dependencies still gate lifecycle eligibility.

## 3. Core project validation

```bash
node .executionkit/runtime/kit/bin/doctor.mjs .
node .executionkit/runtime/kit/bin/validate-execution.mjs .
node .executionkit/runtime/kit/bin/validate-agents.mjs .
node .executionkit/runtime/kit/bin/validate-deep-integration.mjs . --json
node .executionkit/runtime/kit/bin/validate-quality.mjs .
node .executionkit/runtime/kit/bin/validate-seo.mjs .
node .executionkit/runtime/kit/bin/validate-ci-policy.mjs .
node .executionkit/runtime/kit/bin/validate-v30-project.mjs .
```

`validate-operator-entry.mjs` is a distribution-level validator for the ExecutionKit repository itself; do not run it with Bunova as its root. Bunova's project-side operator policy is validated by `validate-v30-project.mjs`, `validate-execution.mjs` and Doctor.

Record actual output and exit codes as evidence. Static repository inspection is not a substitute for these executable checks, and inability to execute them is `NOT_RUN`, not PASS. Fix Bunova-specific failures before advancing canonical task state.

## 4. Agent routing checks

Use the pinned runtime's task-context/agent commands to resolve representative K00, backend, Flutter/offline, platform/SaaS, integration, premium UI, audit and review tasks. Confirm that ordinary K00 work resolves to the dedicated ExecutionKit integrator while `K00-019` resolves independently to the audit specialist. Confirm reviewer separation and that Bunova-native resources are actually reachable.

Useful current entrypoints include:

```bash
node .executionkit/runtime/kit/bin/agent-doctor.mjs .
node .executionkit/runtime/kit/bin/resolve-task-context.mjs . --next --json
node .executionkit/runtime/kit/bin/next-readiness.mjs . --json
node .executionkit/runtime/kit/bin/next-execution.mjs . --json
```

The host adapter/plugin entries are discovery aliases only; canonical role/skill/rule/workflow/contract files remain the authorities named by `.agents/manifest.json`.

## 5. State OS proof

Create a real checkpoint, rehydrate against current repository truth and run the zero-context Amnesia Test using the current pinned State OS commands:

```bash
node .executionkit/runtime/kit/bin/state-checkpoint.mjs .
node .executionkit/runtime/kit/bin/state-rehydrate.mjs .
node .executionkit/runtime/kit/bin/state-amnesia-test.mjs .
```

A file created manually is not equivalent to this proof. State/Memory never becomes task authority.

## 6. Host / MCP integration

The repository contains the current ExecutionKit Antigravity adapter surfaces generated/reconciled from the pinned runtime under `.agents/`. On the actual host where Bunova will execute, prove workspace plugin discovery, hook invocation and MCP handshake with representative read-only/tool calls. Do not commit secrets or host-specific credentials. Memory MCP, if available, remains advisory and must not store canonical task state.

Project-local MCP entrypoint:

```bash
node .executionkit/runtime/kit/bin/mcp-server.mjs .
```

If plugin/MCP discovery requires an Antigravity restart after pull, request that restart exactly once at the host boundary, then resume through `TODO.md` and re-run the relevant proof. Never infer success merely because `.agents/plugins/executionkit` exists.

## 7. Independent audit and activation

After all preceding K00 implementation/review tasks and required host evidence are accepted, execute `K00-019` through the independent audit route. Audit all eight systems, native authority preservation, routing, projection parity, Premium/SEO applicability, broken paths, duplicate backlog risk and accidental product implementation.

Do not set `projectState=ACTIVE` by hand. Only after `K00-019` is accepted may the current pinned activation path close `K00-GATE`; then re-run deep integration with gate acceptance required before opening P01.
