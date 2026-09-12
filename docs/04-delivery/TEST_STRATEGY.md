# Test Strategy

## Test pyramid by risk

### Unit/domain tests
Money calculation, discounts/tax/rounding, timed rating, rate precedence, unit conversions, recipe consumption, entitlement ledgers, capability dependency rules and state machines.

### Feature/integration tests
Orders/billing/payments, cash shifts, venue sessions, production routing, inventory ledger, reservations, permissions, fiscal queue, Menuza contracts, MikroTik adapter fakes and sync ingestion.

### Contract tests
Versioned payloads for Menuza, fiscal adapter, payment providers, router adapters, event schemas and offline command ingestion.

### End-to-end/UI tests
Critical POS/table/gaming/Wi-Fi/production/admin journeys, including RTL and offline/reconnect cases.

### Visual regression
High-value shells/components/states in Arabic/English and responsive sizes; not a substitute for interaction tests.

## Mandatory invariants

- bill arithmetic and payment allocation reconcile exactly;
- no cross-tenant data access;
- closed cash shift cannot mutate silently;
- stock ledger balances from movements;
- timed session rating is reproducible from stored segments/rate snapshot;
- duplicate idempotent command/order/payment/fiscal callbacks do not double-apply;
- loyalty/stored-value balance equals ledger projection;
- fiscal retry does not duplicate transaction identity;
- sync convergence produces one authoritative result or explicit reconciliation exception.

## Phase execution

Run focused tests while implementing risky tasks. Run the relevant full phase suite at phase close. Heavy self-hosted/complete regression is a phase/wave gate, not mandatory after every trivial task unless a task explicitly requires it.

## Fixtures

Test factories/fixtures are clearly non-production. No production seed path may create fake customers/sales/revenue.

## Performance

Benchmark catalog sync, POS search/cart, order submission, production queue, live-control read models, sync backlog replay and reporting. Establish budgets during P01/P03 and enforce once measured.
