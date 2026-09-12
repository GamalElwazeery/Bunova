# Audit Strategy

## Audit cadence

Each phase ends with an independent audit before its exit gate. Audits inspect implementation truth, not task labels.

## Audit dimensions

- **Product completeness** — missing states/workflows/edge cases.
- **Architecture** — boundary violations, duplicate engines, coupling, hidden sources of truth.
- **Data integrity** — history mutation, rounding, ledger/reconciliation defects.
- **Security/privacy** — tenant escape, permission bypass, secret leakage, unsafe logs.
- **Offline/sync** — stale/conflict/replay/data-loss risks.
- **UX quality** — generic/basic/poor workflows, missing states, responsive/RTL/accessibility defects, inconsistent component use.
- **Content** — placeholder text, untranslated strings, fake demo data, unclear errors/help.
- **Testing** — untested invariants, brittle mocks, skipped/ignored tests.
- **Launch** — missing observability, backup/restore, migration/support/runbook.

## Anti-generic UX audit

Operational screens must be reviewed against real jobs: cashier at rush hour, waiter one-handed, barista with queue, gaming supervisor managing many timers, manager resolving variance. Flag forms/tables/cards that simply expose database columns without workflow design.

## Finding severity

P0 launch blocker: data loss/security/financial/fiscal critical failure or core journey impossible.

P1 major: serious operational gap, incorrect offline/permission behavior, major premium UX failure.

P2 minor: polish/usability/documentation issue not breaking core integrity.

Unresolved P0/P1 findings become canonical TODO tasks; no shadow audit backlog.
