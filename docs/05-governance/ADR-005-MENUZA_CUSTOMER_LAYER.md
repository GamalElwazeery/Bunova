# ADR-005: Menuza as Customer-facing Menu/Ordering Layer

**Status:** Accepted

## Decision
Do not rebuild a parallel Bunova digital-menu/public-ordering product. Integrate Menuza through versioned catalog, QR context, order and status contracts.

## Why
Menuza already owns this product surface. Duplication would split catalog/customer experience and waste implementation effort.

## Consequences
Bunova owns operational acceptance/fulfillment/payment/stock/fiscal truth and publishes it; integration drift/reconciliation is a core concern.
