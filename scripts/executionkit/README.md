# Bunova ExecutionKit Host Runbook

Run from Bunova repository root on an authenticated workstation. The runtime is the exact submodule pin; do not run an arbitrary global kit checkout for acceptance evidence.

## 1. Materialize exact runtime

```bash
git submodule sync --recursive
git submodule update --init --recursive
git -C .executionkit/runtime/kit rev-parse HEAD
cat .executionkit/runtime/kit/VERSION
```

Expected runtime identity for this K00 baseline: `fb49bfa3a16995b4ea785ed6ca66d0da808032c8`, published VERSION `3.0.0`.

## 2. Generate canonical derived task projection

```bash
node scripts/executionkit/project-tasks.mjs
```

Do not edit `.executionkit/task-projection.json`; it is regenerated and hash-bound to `TODO.md` / `TODO_ARCHIVE.md`.

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

Record actual output and exit codes as evidence. Static repository inspection is not a substitute for these executable checks, and inability to execute them is `NOT_RUN`, not PASS.

## 4. Agent routing checks

Use the pinned runtime's task-context/agent commands to resolve representative K00, backend, Flutter/offline, platform/SaaS, integration, premium UI, audit and review tasks. Confirm that ordinary K00 work resolves to the dedicated ExecutionKit integrator while `K00-019` resolves independently to the audit specialist. Confirm reviewer separation and that Bunova-native resources are actually reachable.

Useful current entrypoints include:

```bash
node .executionkit/runtime/kit/bin/agent-doctor.mjs .
node .executionkit/runtime/kit/bin/resolve-task-context.mjs . --next --json
node .executionkit/runtime/kit/bin/next-readiness.mjs . --json
node .executionkit/runtime/kit/bin/next-execution.mjs . --json
```

## 5. State OS proof

Create a real checkpoint, rehydrate against current repository truth and run the zero-context Amnesia Test using the current pinned State OS commands:

```bash
node .executionkit/runtime/kit/bin/state-checkpoint.mjs .
node .executionkit/runtime/kit/bin/state-rehydrate.mjs .
node .executionkit/runtime/kit/bin/state-amnesia-test.mjs .
```

A file created manually is not equivalent to this proof.

## 6. Host / MCP integration

The repository must first contain the current ExecutionKit Antigravity adapter surfaces generated from the pinned runtime. On the actual host where Bunova will execute, prove the hook and MCP handshake with representative read-only/tool calls. Do not commit secrets or host-specific credentials. Memory MCP, if available, remains advisory and must not store canonical task state.

Project-local MCP entrypoint:

```bash
node .executionkit/runtime/kit/bin/mcp-server.mjs .
```

## 7. Activation

Do not set `projectState=ACTIVE` by hand. First accept the independent K00 audit according to canonical TODO/evidence and then use the current pinned activation command. Re-run deep integration with gate acceptance required before opening P01.
