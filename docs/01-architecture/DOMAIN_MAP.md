# Domain Map

## Core bounded contexts

### Platform SaaS & Tenant Lifecycle
Bunova Cloud plan versions, trials/subscriptions, commercial entitlements/limits, tenant lifecycle, platform billing/reconciliation and platform-operator support controls. This context determines what an organization is commercially entitled to use but never owns the café's customer sales/bills/payments.

### Identity & Tenancy
Organizations, brands, branches, users, staff identities, roles, permissions, devices/registers and security sessions.

### Capability Configuration
Enabled capabilities, dependencies, branch overrides, onboarding presets and configuration validation. It never owns business history. A capability may be enabled only when Platform SaaS entitlement, configuration dependencies and authorization all allow it.

### Catalog & Pricing
Products, categories, variants, modifier groups/options, units, tax classes, price lists, branch overrides, availability rules and publication state.

### Orders
Customer intent accepted into Bunova: channels, order lines, modifiers, notes, fulfillment context and lifecycle. Orders do not own settled financial truth.

### Billing
Billable lines, calculations, service charges, discounts, tips, taxes, bill splits, bill finalization and invoice/receipt snapshots. Billing is the convergence point for merchandise and services sold **by the café to its customers**.

### Payments
Tenders, allocations, authorizations/captures where supported, cash/card/wallet/external references, refunds and settlement metadata for café commerce.

### Cash & Shifts
Cash drawers, cashier shifts, opening float, movements, blind close/count and reconciliation.

### Venue
Floors, zones, tables, rooms, occupancy and café service sessions. Venue sessions bind customer context, table/resource usage and open bill/order context.

### Production
Stations, routing tickets, production items, preparation states, handoff/serve states, timers and exceptions/remakes.

### Inventory
Stock items, stores, units/conversions, stock ledger, recipes/BOM consumption, waste, counts and transfers.

### Procurement
Suppliers, purchase orders, receiving, supplier operational dues/payments and procurement-side stock entry without becoming a general ledger.

### Staff Operations
Operational roster, assignment, attendance hooks, opening/closing checklists, tips/commission rules, staff-consumption policies and optional Payroll-Lite workforce-cost movements; not statutory payroll ERP.

### Timed Resources
Generic billable resource definitions, rate plans, time sessions, pause/resume/transfer and usage charges.

### Gaming
Gaming-specific extension over timed resources: console/PC/device, controller/player semantics, room/package/membership rules and gaming reservations.

### Wi-Fi
Router connections, access packages, voucher/credential lifecycle including batches, quotas, entitlements, connected-session metadata and captive-portal integration.

### Shisha
Shisha-specific catalog/recipe/service and production behavior integrated with catalog, inventory, production and venue.

### Customer Programs
Customer profiles/consent, loyalty ledger, promotions/rewards, memberships, prepaid credits/bundles, gift-value ledger and optional controlled house-account/customer-tab credit.

### Reservations
Future allocation of tables/rooms/timed resources, deposits, conflicts, arrival/no-show/cancel and conversion into live sessions.

### Menuza Integration
Publication and ingestion boundary; does not duplicate Menuza UI/CMS ownership.

### Fiscal
Egypt fiscal identity, document mapping, submission queue, response history, fiscal status and reconciliation.

### Analytics
Read models/projections only. It must not become a hidden operational source of truth.

## Key upstream/downstream relationships

Platform SaaS grants commercial entitlements to an Organization. Capability Configuration decides which entitled capabilities are actually enabled. Staff/Device authorization decides who may use them. These layers are separate and all must pass.

Catalog feeds Orders, Production routing, Inventory recipes and Menuza publication.

Orders create fulfillment intent and candidate billing lines. Billing finalizes price/tax/discount truth. Payments settle bills. Fiscal records represent finalized fiscal transactions.

Venue and Timed Resources create usage/session facts that Billing can charge. Gaming specializes Timed Resources rather than implementing a second time engine.

Production consumes order intent and reports fulfillment state. Inventory consumes accepted production/sale facts according to configured depletion policy.

Customer Programs may grant entitlements/discounts/credits but Billing/Payments remain the café financial authorities.

## Forbidden ownership leakage

- Platform SaaS billing/subscription records must never be mixed with café customer Bills/Payments.
- Analytics must never mutate operational order/payment truth.
- Menuza integration must never own Bunova stock or settlement truth.
- Gaming must never implement a separate payment engine.
- Wi-Fi packages must never bypass Billing when sold.
- Inventory must not derive historical financial price from current catalog price.
- Capability toggles or plan downgrades must not delete domain data.
- Platform support tooling must not become an alternate operational write authority.
