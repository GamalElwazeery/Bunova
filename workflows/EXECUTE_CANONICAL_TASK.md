# Workflow: Execute a Canonical Task

1. Confirm task marker/dependencies/authority.
2. Inspect existing implementation and tests.
3. Refine planning only if a real ambiguity blocks correct implementation.
4. Implement smallest coherent solution that fully satisfies acceptance; do not intentionally leave hidden partial behavior.
5. Add/update focused tests and observability/evidence.
6. Exercise negative/permission/offline/integration states as applicable.
7. Update affected stable docs/contracts if implementation reveals an accepted truth change.
8. Run focused verification.
9. Mark `[x]` only if Definition of Done passes; otherwise `[~]` or `[!]` with exact reason.
10. Update `TODO.md` immediately.

Commit/push policy can be supplied by the execution environment/kit later; task integrity does not depend on a specific VCS cadence.
