---
name: ek-next
description: Resolve the next canonical ExecutionKit lifecycle action and required context.
---
Run `node .executionkit/runtime/kit/bin/next-execution.mjs . --json` then `node .executionkit/runtime/kit/bin/resolve-task-context.mjs . --next --json`. Execute only the current authorized lifecycle action. Do not create parallel sessions.
