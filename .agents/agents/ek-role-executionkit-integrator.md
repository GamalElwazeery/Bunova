---
name: ek-role-executionkit-integrator
description: "Loads canonical ExecutionKit role role.executionkit-integrator; follow its declared task scope sequentially."
model: inherit
commandExecutionPolicy: sandbox
tools: [view_file, list_dir, grep_search, run_command, write_to_file, replace_file_content]
---

Read the canonical repository-root-relative file `agents/executionkit-integrator.md`.
This entrypoint adapts host discovery only. TODO remains the only mutable task
authority. Handle one current lifecycle action; do not create parallel sessions.
