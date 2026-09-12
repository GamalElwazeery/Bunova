# System Architecture

## Architectural style

Bunova is planned as a modular monolith first, with explicit domain boundaries, versioned integration contracts, asynchronous events where they add resilience, and extraction seams for high-load/integration-heavy components later.

The architectural objective is not microservice count. It is **one coherent operational truth with low coupling between bounded contexts**.

## Planned top-level components

### Bunova Backend
Laravel application owning tenancy, catalog operational truth, orders, billing, payments, venue, production orchestration, inventory, staff operations, timed resources, gaming, Wi-Fi, customer programs, fiscal orchestration, analytics projections and administration APIs.

### Bunova Admin / Owner Web
Livewire/Tailwind surfaces for setup, central control, reporting, configuration and back-office operations. Dense operational pages may use purpose-built reactive components rather than generic CRUD tables.

### Bunova POS / Operations Apps
Flutter applications/shells for POS, waiter/handheld, bar/KDS operational screens where device-native/offline behavior matters. The final packaging may combine roles into one configurable app, but domain permissions and device modes remain explicit.

### Local Operational Store
SQLite/Drift on supported Flutter devices for branch/device-scoped catalog projection, configuration, open shifts/orders/sessions, command queue/outbox, sync metadata and resilient local operation.

### Realtime Layer
Laravel Reverb/WebSockets for low-latency status propagation within a branch: production updates, table/session status, service calls, device/queue changes. Realtime transport is an optimization; durable truth must not depend on an ephemeral socket message.

### Queue / Job Layer
Redis-backed queues for fiscal submission, Menuza synchronization, analytics projection, notifications, exports and integration retries. Every externally visible side effect that can retry must be idempotent.

## Domain modules

- Identity & Tenancy
- Capability & Configuration
- Catalog & Pricing
- Orders
- Billing & Payments
- Cash & Shift Control
- Venue & Service Sessions
- Production
- Inventory & Procurement
- Staff Operations
- Timed Resources
- Gaming
- Wi-Fi
- Shisha
- Customer & Loyalty
- Reservations
- Menuza Integration
- Fiscal & Financial Controls
- Analytics & Reporting
- Notifications
- Hardware/Peripheral Integration
- Audit & Observability

## Cross-cutting rules

### Tenant and branch scope
Every mutable operational aggregate belongs to an organization and, where operational, a branch. Cross-branch access is permissioned and explicit. Never infer tenant scope only from UI routing.

### Historical truth
Price, tax, modifier, customer, product and resource names may change. Completed financial/operational records therefore preserve snapshots required to explain what happened at the time.

### Monetary precision
Money uses integer minor units or fixed decimal rules chosen centrally; floating-point arithmetic is prohibited for financial calculations.

### Time
Store canonical timestamps in UTC plus the business timezone context required for business-day/shifts/reporting. Timed-session billing must use monotonic elapsed-time semantics on device where clock drift could affect charging, then reconcile with server timestamps.

### IDs
Use globally unique identifiers suitable for offline creation for operational aggregates. Human-facing order/session/receipt numbers are separate scoped sequences, never primary keys.

### Idempotency
Order intake, payment callbacks, fiscal submission, Menuza ingestion, sync commands and integration side effects require idempotency keys and replay-safe handlers.

### Audit
Sensitive mutations emit immutable audit events with actor/device/branch/context/reason and meaningful before/after or domain-change detail.

## Deployment shape

Start with one backend deployment plus PostgreSQL, Redis, object storage and Reverb, with workers horizontally scalable. Integration adapters are isolated modules/process workers where failure containment is useful. Analytics begins with transactional-safe projections and PostgreSQL reporting; a separate analytics store is introduced only when measured scale justifies it.
