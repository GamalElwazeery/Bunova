# Test and Audit Rules

- Risk determines test depth.
- Money, tax, time rating, stock ledger, sync, authorization, fiscal and stored value require deterministic automated coverage.
- Test denial/failure/retry/duplicate/offline behavior, not happy path only.
- Focused tests may run per task; heavy/full suite runs at phase/wave gate unless task risk explicitly requires earlier.
- A green suite does not waive UX/manual/visual audit where applicable.
- Audit findings become canonical `TODO.md` tasks; no shadow backlog.
- Open P0/P1 findings prevent the phase/launch gate from closing.
- Skipped tests require explicit reason and canonical follow-up; silently disabled tests are defects.
