# Domain Map

## Core bounded contexts

### Identity & Tenancy
Organizations, brands, branches, users, staff identities, roles, permissions, devices/registers and security sessions.

### Capability Configuration
Enabled capabilities, dependencies, branch overrides, onboarding presets and configuration validation. It never owns business history.

### Catalog & Pricing
Products, categories, variants, modifier groups/options, units, tax classes, price lists, branch overrides, availability rules and publication state.

### Orders
Customer intent accepted into Bunova: channels, order lines, modifiers, notes, fulfillment context and lifecycle. Orders do not own settled financial truth.

### Billing
Billable lines, calculations, service charges, discounts, tips, taxes, bill splits, bill finalization and invoice/receipt snapshots. Billing is the convergence point for merchandise and services.

### Payments
Tenders, allocations, authorizations/captures where supported, cash/card/wallet/external references, refunds and settlement metadata.

### Cash & Shifts
Cash drawers, cashier shifts, opening float, movements, blind close/count and reconciliation.

### Venue
Floors, zones, tables, rooms, occupancy and café service sessions. Venue sessions bind customer context, table/resource usage and open bill/order context.

### Production
Stations, routing tickets, production items, preparation states, handoff/serve states, timers and exceptions/remakes.

### Inventory
Stock items, stores, units/conversions, stock ledger, recipes/BOM consumption, waste, counts and transfers.

### Procurement
Suppliers, purchase orders, receiving and procurement-side stock entry.

### Staff Operations
Operational roster, assignment, attendance hooks, tips/commission rules and staff-consumption policies; not full payroll.

### Timed Resources
Generic billable resource definitions, rate plans, time sessions, pause/resume/transfer and usage charges.

### Gaming
Gaming-specific extension over timed resources: console/PC/device, controller/player semantics, room/package/membership rules and gaming reservations.

### Wi-Fi
Router connections, access packages, voucher/credential lifecycle, quotas, entitlements, connected-session metadata and captive-portal integration.

### Shisha
Shisha-specific catalog/recipe/service and production behavior integrated with catalog, inventory, production and venue.

### Customer Programs
Customer profiles/consent, loyalty ledger, rewards, memberships, prepaid credits/bundles and gift-value ledger.

### Reservations
Future allocation of tables/rooms/timed resources, deposits, conflicts, arrival/no-show/cancel and conversion into live sessions.

### Menuza Integration
Publication and ingestion boundary; does not duplicate Menuza UI/CMS ownership.

### Fiscal
Egypt fiscal identity, document mapping, submission queue, response history, fiscal status and reconciliation.

### Analytics
Read models/projections only. It must not become a hidden operational source of truth.

## Key upstream/downstream relationships

Catalog feeds Orders, Production routing, Inventory recipes and Menuza publication.

Orders create fulfillment intent and candidate billing lines. Billing finalizes price/tax/discount truth. Payments settle bills. Fiscal records represent finalized fiscal transactions.

Venue and Timed Resources create usage/session facts that Billing can charge. Gaming specializes Timed Resources rather than implementing a second time engine.

Production consumes order intent and reports fulfillment state. Inventory consumes accepted production/sale facts according to configured depletion policy.

Customer Programs may grant entitlements/discounts/credits but Billing/Payments remain the financial authorities.

## Forbidden ownership leakage

- Analytics must never mutate operational order/payment truth.
- Menuza integration must never own Bunova stock or settlement truth.
- Gaming must never implement a separate payment engine.
- Wi-Fi packages must never bypass Billing when sold.
- Inventory must not derive historical financial price from current catalog price.
- Capability toggles must not delete domain data.
