# Module Boundary and Outbox Conventions

This document establishes the architecture rules, module dependency constraints, command/use-case conventions, and transactional outbox policy for Bunova's modular monolith.

---

## 1. Modular Monolith Bounded Contexts

Bunova groups code into discrete bounded contexts under `backend/app/Domain/`:
1. `Platform`: SaaS subscriptions, commercial plan versions, tenant entitlements, and limits.
2. `Identity`: Organizations, brands, branches, users, staff identities, roles, permissions, registered devices.
3. `Capability`: Capability registry, dependency graphs, branch overrides, onboarding presets.
4. `Catalog`: Products, categories, variants, modifiers, pricing lists.
5. `Orders`: Order intake, order lines, ticket lifecycle, fulfillment status.
6. `Billing`: Customer bills, line calculations, service charges, taxes, discounts, bill finalization.
7. `Payments`: Tenders, electronic payments, cash allocations, refunds.
8. `Cash`: Cash drawer shifts, floats, drops, audits.
9. `Venue`: Floors, tables, rooms, service sessions.
10. `Production`: Kitchen/bar station tickets, routing, KDS displays.
11. `Inventory`: Stock ledger, stores, recipes/BOM depletion, waste, counts.
12. `Procurement`: Supplier purchase orders, receiving, operational dues.
13. `Staff`: Staff rosters, shifts, attendance, tips, Payroll-Lite.
14. `Timed`: Timed resources, hourly rate engines, session intervals.
15. `Gaming`: Gaming lounge consoles, controller tracking, packages.
16. `Wifi`: Router integrations, voucher generation, captive portal.
17. `Shisha`: Shisha preparation, coal maintenance, station routing.
18. `Customer`: Customer profiles, loyalty ledger, memberships, tabs.
19. `Reservations`: Future advance bookings for tables, rooms, resources.
20. `Fiscal`: Fiscal authority integrations (e.g. ETA eReceipt).
21. `Audit`: Immutable audit events per `AUDIT_EVENT_CONTRACT.md`.

---

## 2. Hard Invariants & Dependency Rules

### Invariant 1: Platform SaaS vs Café Operations Boundary
- `Platform` domain controls what a tenant is entitled to configure and use.
- `Billing` and `Payments` domains own customer sales to café guests.
- **Rule**: Platform billing/entitlements and café guest bills/payments NEVER share tables, ledger entries, or controllers. A café owner cannot acquire platform privileges through café configuration.

### Invariant 2: Explicit Tenancy Scoping
- Every mutable café aggregate requires explicit `organization_id` and, where operational, `branch_id`.
- Cross-branch access is strictly permissioned and explicit; never inferred from UI routing.

### Invariant 3: Clean Cross-Domain Communication
- Direct cross-domain foreign-key joins across bounded contexts are minimized.
- Cross-domain mutations MUST NOT mutate another domain's private models directly.
- Cross-domain side-effects are driven via:
  1. **Domain Commands** executed through explicit services/use cases.
  2. **Transactional Outbox Events** published atomically and consumed idempotently.

---

## 3. Domain Command & Use-Case Conventions

Every state-mutating operation follows the Command/Use-Case convention:
- **`DomainCommand`**: An immutable data transfer object containing:
  - `commandId`: UUIDv7
  - `organizationId`: UUID
  - `branchId`: Optional UUID
  - `actorId` & `actorType`: Identity of who requested the action (`staff`, `user`, `device`, `system`)
  - `correlationId`: For end-to-end request tracing
  - `parameters`: Typed command payload
- **`CommandHandler`**:
  - Validates input and preconditions.
  - Enforces server-side authorization and branch scoping.
  - Executes inside a database transaction (`DB::transaction(...)`).
  - Mutates aggregate state.
  - Writes domain event(s) to the **Transactional Outbox** inside the same transaction.
  - Returns a typed `UseCaseResult`.

---

## 4. Transactional Outbox Policy

To prevent dual-write failure (e.g., database updated but message bus / queue fails, or vice versa):
1. **Atomic Write**: Domain state mutation and corresponding `OutboxEvent` are written to the database in the same database transaction.
2. **Rollback Guarantee**: If the transaction fails or throws an exception, both the state mutation and outbox event are rolled back atomically.
3. **Asynchronous Dispatch**: An outbox worker or queue listener polls/dispatches pending outbox events asynchronously to Redis/Reverb/external consumers.
4. **Idempotency**: Downstream event consumers must be idempotent, recording the outbox `event_id` as their idempotency key.

---

## 5. Summary Schema for Outbox Events

Table: `outbox_events`
- `id`: UUIDv7 (time-ordered primary key)
- `event_name`: string (e.g. `order.created`, `bill.finalized`)
- `aggregate_type`: string (e.g. `order`, `bill`)
- `aggregate_id`: string (UUID)
- `organization_id`: string (UUID)
- `branch_id`: nullable string (UUID)
- `correlation_id`: string (UUID)
- `actor_id`: string
- `actor_type`: string (`staff`, `user`, `device`, `system`)
- `payload`: JSON
- `status`: string (`pending`, `processing`, `dispatched`, `failed`)
- `attempts`: integer
- `dispatched_at`: nullable timestamp
- `last_error`: nullable text
