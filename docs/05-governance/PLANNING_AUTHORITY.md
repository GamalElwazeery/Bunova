# Planning Authority

## Authority hierarchy

When sources disagree, use this order unless an ADR explicitly supersedes it:

1. Current live repository state and accepted ADRs.
2. `TODO.md` for current execution/backlog state.
3. Product/domain contracts and ownership documents.
4. Architecture/domain plans.
5. Rules and skills.
6. Workflows and examples.
7. Historical notes/prompts/checkpoints.

A newer accepted authority must update affected downstream references; silent contradiction is not allowed.

## Mutable vs stable authorities

`TODO.md` is intentionally mutable and records current task state. Product, architecture, contract and rule documents are stable authorities changed only when product truth changes. Do not use stable documents as hidden execution trackers.

## Planning completeness criteria

Planning is implementation-ready only when:

- every major domain has explicit ownership, entities/terms, invariants, lifecycle and integration boundaries;
- cross-domain money/time/stock/fiscal/event behavior is contractually defined;
- phase dependency order is coherent;
- every canonical task has sufficient execution detail or a precise reference to it;
- acceptance/test/evidence requirements exist;
- no critical decision is left as "TBD" without a blocking canonical task;
- external integrations define failure/retry/idempotency/reconciliation behavior;
- offline and permission behavior is planned, not deferred as polish;
- launch, migration, support and rollback are included.

## Change control

When implementation reveals a planning defect:

1. stop inventing local behavior;
2. identify the highest affected authority;
3. update/approve the canonical decision;
4. update downstream contracts/tasks/references;
5. execute from the corrected task truth.
