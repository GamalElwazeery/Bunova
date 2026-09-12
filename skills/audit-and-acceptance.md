# Skill: Audit and Acceptance

## Use when
Closing a task/phase or performing deep/honest/full audit.

## Audit order

1. Read canonical requirement and acceptance.
2. Inspect actual implementation/schema/tests, not labels.
3. Exercise happy path and high-risk negative states.
4. Check tenant/permission/offline/history/integration implications.
5. Review UX for all required states and non-generic quality.
6. Search TODO/FIXME/mock/demo/placeholder/skipped tests and suspicious shortcuts.
7. Compare docs/contracts with behavior.
8. Classify findings P0/P1/P2.
9. Put unresolved findings into canonical `TODO.md`; never create a shadow audit backlog.

Evidence must be reproducible.
