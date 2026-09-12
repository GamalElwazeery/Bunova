# Fiscal, Expenses and Financial Controls

## Egypt fiscal boundary

Bunova prepares a normalized immutable fiscal transaction snapshot from finalized billing/payment truth. The ETA adapter maps it to the current eReceipt document/API requirements, including registered POS/device identity, codes, document type, UUID/sequence relationships and return/correction flows as legally required at implementation time.

The exact ETA schema and rollout obligations must be revalidated against official documentation before implementation/production; never hard-code planning-era assumptions as permanent law.

## Fiscal state

`not_required`, `pending`, `queued`, `submitted`, `accepted`, `rejected`, `retrying`, `reconciliation_required` with detailed provider response history.

Financial success and fiscal submission status are related but separate facts. A provider outage cannot erase a sale.

## Idempotency/retry

Submission payload has stable identity/hash and idempotent retry policy. Unknown timeout results are queried/reconciled before duplicate submission where the ETA contract requires it.

## Returns/refunds

Refund/correction creates the legally appropriate return/correction fiscal workflow linked to original fiscal document and Bunova refund.

## Expenses/petty cash

Operational expenses can be recorded against branch/cash shift with category, amount, payee/note, attachment optional, permission/approval and cash-movement linkage. This is not full double-entry accounting.

## Day close

Operational close reconciles cash shifts, external tender totals, refunds/voids, expenses, pending/failed fiscal documents and selected stock/venue exceptions. Close produces an immutable summary snapshot and unresolved-exception list.

## Audit

Price overrides, refunds, fiscal corrections, expense edits before finalization, and reconciliation actions require actor/reason/history.
