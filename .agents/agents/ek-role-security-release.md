---
name: ek-role-security-release
description: "Loads canonical ExecutionKit role role.security-release; follow its declared task scope sequentially."
model: inherit
commandExecutionPolicy: sandbox
tools: [view_file, list_dir, grep_search, run_command, write_to_file, replace_file_content]
---

Read the canonical repository-root-relative file `agents/security-release-engineer.md`.
This entrypoint adapts host discovery only. TODO remains the only mutable task
authority. Handle one current lifecycle action; do not create parallel sessions.
