# Bunova ExecutionKit Host Runbook

Run from Bunova repository root on an authenticated workstation. The runtime is the exact submodule pin; do not run an arbitrary global kit checkout for acceptance evidence.

## 1. Materialize exact runtime

```bash
git submodule sync --recursive
git submodule update --init --recursive
git -C .executionkit/runtime/AI-ExecutionKit rev-parse HEAD
```

Expected runtime identity for this K00 baseline: `fb49bfa3a16995b4ea785ed6ca66d0da808032c8`.

## 2. Generate canonical derived task projection

```bash
node scripts/executionkit/project-tasks.mjs
```

Do not edit `.executionkit/task-projection.json`; it is regenerated and hash-bound to `TODO.md` / `TODO_ARCHIVE.md`.

## 3. Core project validation

```bash
node .executionkit/runtime/AI-ExecutionKit/bin/doctor.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-execution.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-agents.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-deep-integration.mjs . --json
node .executionkit/runtime/AI-ExecutionKit/bin/validate-quality.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-seo.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-ci-policy.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-operator-entry.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/validate-v30-project.mjs .
```

Record actual output/exit codes as evidence; do not summarize failures away.

## 4. Agent routing checks

Use the pinned runtime's task-context/agent commands to resolve representative backend, Flutter/offline, platform/SaaS, integration, premium UI, audit and review tasks. Confirm reviewer separation and that Bunova-native resources are actually reachable.

Useful current entrypoints include:

```bash
node .executionkit/runtime/AI-ExecutionKit/bin/agent-doctor.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/next-readiness.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/next-execution.mjs .
```

## 5. State OS proof

Create a real checkpoint, rehydrate against current repository truth and run the zero-context Amnesia Test using the current pinned State OS commands:

```bash
node .executionkit/runtime/AI-ExecutionKit/bin/state-checkpoint.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/state-rehydrate.mjs .
node .executionkit/runtime/AI-ExecutionKit/bin/state-amnesia-test.mjs .
```

A file created manually is not equivalent to this proof.

## 6. Host / MCP integration

Run the current Antigravity integration only on the actual host where Bunova will execute, then prove MCP handshake and representative tool calls. Do not commit secrets or host-specific credentials. Memory MCP, if available, remains advisory and must not store canonical task state.

## 7. Activation

Do not set `projectState=ACTIVE` by hand. First accept the independent K00 audit according to canonical TODO/evidence and then use the current pinned activation command. Re-run deep integration with gate acceptance required before opening P01.
