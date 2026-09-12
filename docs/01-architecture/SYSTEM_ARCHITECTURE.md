# System Architecture

## Architectural style

Bunova is planned as a modular monolith first, with explicit domain boundaries, versioned integration contracts, asynchronous events where they add resilience, and extraction seams for high-load/integration-heavy components later.

The architectural objective is not microservice count. It is **one coherent product with explicit operational and commercial truths and low coupling between bounded contexts**.

## Planned top-level components

### Bunova Backend / Cloud
Laravel application owning tenant identity, platform plan/subscription/entitlement state, catalog operational truth, orders, café billing/payments, venue, production orchestration, inventory, staff operations, timed resources, gaming, Wi-Fi, customer programs, fiscal orchestration, analytics projections and administration APIs. Platform SaaS billing and café customer billing remain separate bounded contexts inside the modular architecture.

### Café Owner / Admin Web
Livewire/Tailwind surfaces for café setup, central control, reporting, configuration and back-office operations. Dense operational pages may use purpose-built reactive components rather than generic CRUD tables.

### Bunova Platform Admin
Separately authorized web surface for Bunova operators to manage tenant lifecycle, plan versions, trials/subscriptions, commercial entitlements/limits, SaaS billing/reconciliation, service health and controlled support operations. Platform role boundaries are distinct from tenant roles.

### Bunova Public Website / Commercial Onboarding
Arabic/English responsive product, pricing/plan, lead/signup/trial, legal/help and SEO/content surface. It does not replace Menuza public café menus.

### Bunova POS / Operations Apps
Flutter applications/shells for POS, waiter/handheld, bar/KDS and gaming operational screens where device-native/offline behavior matters. The final packaging may combine roles into one configurable app, but domain permissions and device modes remain explicit.

### Local Operational Store
SQLite/Drift on supported Flutter devices for branch/device-scoped catalog projection, configuration, commercial-entitlement projection, open shifts/orders/sessions, command queue/outbox, sync metadata and resilient local operation.

### Realtime Layer
Laravel Reverb/WebSockets for low-latency status propagation within a branch: production updates, table/session status, service calls, device/queue changes. Realtime transport is an optimization; durable truth must not depend on an ephemeral socket message.

### Queue / Job Layer
Redis-backed queues for fiscal submission, Menuza synchronization, platform subscription/provider reconciliation, analytics projection, notifications, exports and integration retries. Every externally visible side effect that can retry must be idempotent.

## Domain modules

- Platform SaaS & Tenant Lifecycle
- Identity & Tenancy
- Capability & Configuration
- Catalog & Pricing
- Orders
- Café Billing & Payments
- Cash & Shift Control
- Venue & Service Sessions
- Production
- Inventory & Procurement
- Staff Operations
- Timed Resources
- Gaming
- Wi-Fi
- Shisha
- Customer & Loyalty / House Accounts
- Reservations
- Menuza Integration
- Fiscal & Financial Controls
- Analytics & Reporting
- Notifications
- Hardware/Peripheral Integration
- Audit & Observability

## Cross-cutting rules

### Platform vs tenant boundary
Commercial Plan/Subscription/Entitlement state controls what a tenant may configure/use, but it never becomes the café's operational financial ledger. Platform Admin authority is separate from café Owner/Admin authority.

### Tenant and branch scope
Every mutable café operational aggregate belongs to an organization and, where operational, a branch. Cross-branch access is permissioned and explicit. Never infer tenant scope only from UI routing.

### Historical truth
Plan versions, prices, taxes, modifiers, customer/product/resource names and other changing master data may change. Completed commercial/financial/operational records preserve snapshots required to explain what happened at the time.

### Monetary precision
Money uses integer minor units or fixed decimal rules chosen centrally; floating-point arithmetic is prohibited for financial calculations in both café and platform billing.

### Time
Store canonical timestamps in UTC plus the business timezone context required for business-day/shifts/reporting. Timed-session billing must use monotonic elapsed-time semantics on device where clock drift could affect charging, then reconcile with server timestamps.

### IDs
Use globally unique identifiers suitable for offline creation for operational aggregates. Human-facing order/session/receipt numbers are separate scoped sequences, never primary keys.

### Idempotency
Order intake, café/payment callbacks, platform subscription/payment callbacks, fiscal submission, Menuza ingestion, sync commands and integration side effects require idempotency keys and replay-safe handlers.

### Audit
Sensitive tenant, platform, financial and operational mutations emit immutable audit events with actor/context/reason and meaningful change detail while respecting data-minimization rules.

## Deployment shape

Start with one backend deployment plus PostgreSQL, Redis, object storage and Reverb, with workers horizontally scalable. Integration adapters are isolated modules/process workers where failure containment is useful. Analytics begins with transactional-safe projections and PostgreSQL reporting; a separate analytics store is introduced only when measured scale justifies it.
