# Workflow: Phase Close

1. Confirm every phase task is `[x]`; resolve any `[~]`/`[!]` or keep gate open.
2. Run phase-level automated suite and required static checks.
3. Run independent audit across product/architecture/data/security/offline/UX/content/testing.
4. Add unresolved findings to canonical `TODO.md` and execute them before closure for P0/P1.
5. Verify docs/contracts/schema examples are synchronized.
6. Verify no unintended regression in completed upstream phases.
7. Capture reproducible evidence/commands/results.
8. Mark the phase gate complete only after criteria pass.
