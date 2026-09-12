# Product Scope and Boundaries

## In scope

### Platform / SaaS
Organizations/tenants, plan versions, trials/subscriptions, commercial entitlements and limits, platform administration/support, tenant lifecycle, SaaS billing/reconciliation, bounded offline entitlement grace, and public commercial onboarding/pricing surfaces. Platform billing is strictly separate from café customer billing.

### Foundation
Organizations, brands, branches, registers/devices, configuration, capabilities, presets, roles/permissions, audit, localization and multi-currency/tax-ready primitives.

### Commerce
Catalog, categories, variants, modifier groups, pricing, availability, counter/takeaway/dine-in/online order ingestion, bills, discounts, promotions, cover/minimum-spend policies, payments, split tender, refunds, cash drawers and shifts.

### Venue
Floors, zones, tables, rooms, service sessions, occupancy, table transfer/merge/split, reservations, waiter assignment and service requests.

### Production
Production stations, routing, barista display, KDS-compatible workflows, item-level states, preparation timing, ready/served handoff and production exceptions.

### Timed services and gaming
Generic timed-resource sessions plus gaming-specific device/controller/player/rate/package/booking behavior.

### Wi-Fi
Router adapters beginning with MikroTik, hotspot/package policy, individual and batch voucher cards, quotas, time/data/speed constraints, issuance/printing/QR, status/revocation and captive-portal integration contracts.

### Inventory and procurement
Ingredients, sellable stock, packaging, recipes/BOM, yield, stock ledger, branch stores, counts, transfers, purchase orders, receiving, suppliers, operational supplier dues/payments, waste and COGS.

### Staff operations
Operational staff profiles, roles, shift assignment, attendance hooks, station/table assignment, permissions, tips/commissions, opening/closing operational checklists, and optional Payroll-Lite workforce costing (wage basis, overtime, advances, bonuses/deductions and payout evidence). Full statutory payroll remains outside the initial frontier.

### Customer growth and value
Customer profiles, loyalty, stamps/points, promotions, memberships, bundles/credits, gift value, optional controlled customer tab/house-account credit, customer notes/preferences and consent-aware communication preferences.

### Menuza integration
Canonical menu publication, branch availability, QR context, customer order intake, order status callbacks and online payment/fulfillment contracts.

### Fiscal and controls
Egypt ETA eReceipt adapter, document sequencing/identity, fiscal queue/retry/reconciliation, expense/petty cash, audit and close-of-day controls.

### Intelligence
Owner control center, live venue status, sales/margin/throughput/utilization/waste/leakage analytics and later anomaly/AI insight hooks.

## Explicit non-goals for first production frontier

- full statutory HR/payroll or general-ledger accounting ERP;
- manufacturing/MRP beyond café recipes and simple prep/yield;
- owning Menuza's public-menu/theme/CMS product;
- reproducing a full restaurant product inside Bunova when shared contracts can be used;
- native delivery-fleet optimization as a first release requirement;
- marketplace aggregator integrations before core order/fiscal/stock truth stabilizes;
- generalized hotel/PMS/banquet management;
- speculative AI chatbot features without reliable operational data.

## Expansion-safe but not assumed

Billiards, coworking seats, meeting rooms, event packages, NFC membership, SoftPOS/payment-terminal adapters, IoT device power control, additional router vendors, franchise royalties and jurisdiction-specific advanced workforce/payroll/accounting modules may be added behind existing domain boundaries.
