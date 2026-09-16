# Bunova Domain Architecture

Bunova is designed as a **modular monolith** first. Each domain within `app/Domain/` represents a discrete bounded context with clear boundaries, minimal direct coupling, and strict ownership of its domain models, contracts, and business logic.

## Core Architectural Invariants
1. **Platform SaaS vs Café Operations**:
   - `Platform/`: Owns Bunova Cloud subscription lifecycle, plan versions, commercial entitlements, and SaaS billing.
   - `Billing/` & `Payments/`: Owns café operational sales to customers, POS checkout, cash drawers, and table billing.
   - **Hard Invariant**: Platform billing and café customer billing NEVER share tables, ledger entries, or controllers.
2. **Tenancy & Scoping**:
   - All café operational entities belong to an `Organization` (`Identity/`) and optionally a `Branch`.
   - Tenant isolation is strictly enforced via global scopes, query boundaries, and explicit foreign keys (`organization_id`, `branch_id`).
3. **Capability Architecture**:
   - `Capability/`: Capabilities (Dine-In, Takeaway, Gaming, Wi-Fi, Shisha, Inventory, KDS) are architectural capabilities enabled per tenant/branch. Presets (Specialty Coffee, Gaming Lounge, etc.) are onboarding configuration, not code forks.
4. **Offline Resilience**:
   - Operational apps (`apps/pos/`) operate against local Drift SQLite and project state asynchronously through idempotent command sync queues.
5. **Compensating Financials**:
   - Financial, fiscal, and inventory movements use history-preserving and compensating ledger semantics rather than destructive updates.

## Domain Directory Structure
- `Platform/`: Platform SaaS & Tenant Lifecycle (plans, entitlements, limits)
- `Identity/`: Organizations, Brands, Branches, Users, Staff, Roles, Devices
- `Capability/`: Capabilities, presets, dependencies, configuration
- `Catalog/`: Products, categories, variants, modifiers, pricing rules
- `Orders/`: Order lifecycle, items, fulfillment states
- `Billing/`: Café customer bills, bill splits, taxes, receipts
- `Payments/`: Tenders, payment allocations, refunds
- `Cash/`: Cash drawers, cashier shifts, float movements
- `Venue/`: Floors, tables, rooms, service sessions
- `Production/`: Kitchen stations, routing tickets, KDS
- `Inventory/`: Stock items, stores, recipes/BOM, waste, counts
- `Procurement/`: Suppliers, purchase orders, receiving, operational dues
- `Staff/`: Staff operations, shifts, attendance, Payroll-Lite
- `Timed/`: Generic timed resources, rate plans, sessions
- `Gaming/`: Gaming lounges, consoles, players, bookings
- `Wifi/`: Wi-Fi router adapters, voucher issuance, quotas
- `Shisha/`: Shisha catalog, modifiers, coal/service workflow
- `Customer/`: Customer profiles, loyalty ledger, house accounts
- `Reservations/`: Table/room/resource reservations
- `Fiscal/`: Fiscal authority adapters (e.g. ETA eReceipt)
- `Audit/`: Immutable audit events per `AUDIT_EVENT_CONTRACT.md`
