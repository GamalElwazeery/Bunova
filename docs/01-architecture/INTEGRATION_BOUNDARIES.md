# Integration Boundaries

## Menuza

### Menuza owns
Public digital menu rendering, QR/public storefront UX, themes/content presentation, customer cart/ordering UX and customer-facing order tracking surfaces that belong to Menuza.

### Bunova owns
Operational catalog truth selected for publication, branch price/availability, acceptance/rejection, order fulfillment, production, stock, bill/payment/fiscal truth and operational status.

### Required contracts
Catalog publication, QR context, order submission, idempotency, branch availability, order status callback/poll, online-payment reference handoff, cancellation/rejection and reconciliation.

No shared database coupling is allowed as an implicit integration strategy.

## Restaurant System

Bunova must review existing reusable restaurant capabilities before implementing overlapping F&B behavior. Reuse options in preferred order:

1. stable shared domain/package;
2. explicit service/API contract;
3. shared specification/contract with independent implementation when deployment constraints demand it.

Copy/paste without ownership/versioning is prohibited.

Candidate reuse areas: advanced KDS/production routing, recipe/unit modeling, table service patterns, reservations, delivery-entry patterns and food-production rules.

Bunova-specific ownership remains: capability composition, café venue sessions, timed resources, gaming, Wi-Fi, café-specific billing convergence and café control center.

## Egypt ETA

Fiscal adapter sits behind a Bunova fiscal domain boundary. Core billing produces a normalized fiscal transaction snapshot; the adapter maps/signs/submits according to the current ETA specification. Provider/API errors never mutate financial history; they change fiscal submission/reconciliation state.

## Payment providers

Define a provider-neutral payment intent/result/refund adapter. Cash remains native. External card/SoftPOS/terminal/wallet adapters expose capability flags (online/offline, refund, partial refund, tip, terminal pairing) and provider references without contaminating core tender semantics.

## MikroTik / router providers

Wi-Fi domain owns normalized packages/vouchers/entitlements. Router adapters translate create/revoke/status/session operations. Router reachability failures are explicit operational states with retry and reconciliation.

## Notifications

Notification providers (SMS/WhatsApp/email/push) consume normalized notification jobs/templates. Provider delivery status is observable but never the source of operational order truth.
