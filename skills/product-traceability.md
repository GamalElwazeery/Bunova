# Skill: Product Traceability

## Use when
Creating/refining tasks, changing product behavior or resolving planning ambiguity.

## Procedure

Trace: product goal -> domain authority -> contract/invariant -> canonical TODO task -> implementation -> test/evidence.

Check that the task has stable ID, concrete outcome, dependencies, authorities, acceptance and evidence. If behavior changes, update the highest affected authority then downstream references. Never patch only the TODO text when a stable product contract changed.

## Quality checks

- no orphan requirement;
- no code without canonical task rationale;
- no vague "implement X" where business behavior is undefined;
- no alternate mutable checklist.
