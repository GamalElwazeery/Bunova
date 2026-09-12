# POS, Orders, Payments and Cash Shifts

## POS operating modes

The same commerce core supports role/device-specific modes: counter, takeaway, waiter/table, gaming/session attachment and manager override. High-frequency counter UI must favor touch speed, favorites/recent items, fast search, modifier presets and quick tender.

## Cart/order creation

A cart captures channel/context, branch/register/operator, customer optional, table/session optional, items/modifiers/notes and pricing preview. Submission validates current capability, catalog availability, pricing version and operator permission.

Offline submission may use cached valid catalog/config and records the exact local version/snapshot used.

## Order identity

Each order uses a globally unique immutable ID plus human branch/day sequence/pickup code. Human sequences are display/reference only.

## Payment rules

- Bill finalization precedes or atomically accompanies payment recording according to workflow.
- Mixed tender is supported.
- Cash records amount tendered/change.
- External card/wallet provider states distinguish initiated, authorized/captured, failed, cancelled, unknown/reconciliation-required.
- Never mark an external payment successful solely because a client timed out optimistically.
- Tips are separately represented and reportable.

## Voids/cancellations/refunds

Before finalization: authorized void/cancel with reason and production impact.

After payment/fiscal finalization: explicit refund/return flow referencing original transaction. Partial refund validates quantities/allocations. Financial history is not deleted.

## Cash drawer and cashier shift

Shift lifecycle: `opening -> open -> closing_count -> reconciled -> closed`, with manager override states if required.

Record opening float, cash sales, cash refunds, cash-in/out, petty-cash movement, drawer-open events where supported, expected balance, blind counted balance and variance.

Operator should count denominations optionally; manager can review variance with reason/approval policy.

## Close blockers

Configurable blockers/warnings: unsettled bills, open venue/timed sessions, pending cash movements, fiscal discrepancies, unclosed subordinate registers.

## Fraud/leakage signals

Void/refund rate, discount overrides, repeated drawer opens, late-shift adjustments, excessive complimentary lines and variance are analytics/risk signals—not automatic accusations.
