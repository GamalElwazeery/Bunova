# Financial and Fiscal Integrity Rules

- Never use binary floating point for money.
- Server/domain layer recalculates canonical totals; client totals are advisory.
- Finalized bill arithmetic must reconcile deterministically.
- Payments and refunds are append-oriented allocations linked to source bills.
- Unknown external payment status is not success or failure; reconcile.
- Refund/return never deletes original financial/fiscal history.
- Cash movement must belong to a drawer/shift context and actor.
- Fiscal failure never deletes a sale; fiscal state is separate and durable.
- Idempotency is mandatory for money/fiscal external operations.
- Any manual override requires permission, reason and audit.
