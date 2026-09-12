---
name: ek-state
description: Rehydrate and checkpoint ExecutionKit State OS for long-running or resumed autonomous work.
---

# ExecutionKit State OS

Use this skill whenever work spans a long session, resumes after interruption/context compaction, changes model/agent/device, or approaches an audit/test/release boundary.

1. Read `executionkit_state_rehydrate` (or `node .executionkit/runtime/kit/bin/state-rehydrate.mjs .`) before relying on conversational memory.
2. If State reports drift, inspect live branch/HEAD/worktree, canonical `TODO.md`/native authority, implementation and evidence before mutation. Preserve interrupted dirty work.
3. During long tasks, call `executionkit_state_checkpoint` at meaningful milestones and within the configured heartbeat window. Record bounded working files, evidence references and a concrete next safe action.
4. Before/after handoff, audit, expensive testing, commits or release-sensitive operations, checkpoint again.
5. Use `executionkit_state_amnesia_test` to prove a zero-context agent can safely reconstruct execution.
6. If a host Memory MCP is available, call `executionkit_state_memory_envelope` and store/retrieve it only as `ADVISORY_ONLY`. Durable reusable decisions/invariants are appropriate; task status, queues, secrets and unverified PASS claims are not.

`TODO.md` or the configured native task authority remains the sole mutable backlog/task-status authority. State OS is derived continuity, never a shadow queue.
