---
name: ek-role-integration-engineer
description: "Loads canonical ExecutionKit role role.integration-engineer; follow its declared task scope sequentially."
model: inherit
commandExecutionPolicy: sandbox
tools: [view_file, list_dir, grep_search, run_command, write_to_file, replace_file_content]
---

Read the canonical repository-root-relative file `agents/integration-engineer.md`.
This entrypoint adapts host discovery only. TODO remains the only mutable task
authority. Handle one current lifecycle action; do not create parallel sessions.
