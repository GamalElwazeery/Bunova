# Product Scope and Boundaries

## In scope

### Foundation
Organizations, brands, branches, registers/devices, configuration, capabilities, presets, roles/permissions, audit, localization and multi-currency/tax-ready primitives.

### Commerce
Catalog, categories, variants, modifier groups, pricing, availability, counter/takeaway/dine-in/online order ingestion, bills, discounts, promotions, payments, split tender, refunds, cash drawers and shifts.

### Venue
Floors, zones, tables, rooms, service sessions, occupancy, table transfer/merge/split, reservations, waiter assignment and service requests.

### Production
Production stations, routing, barista display, KDS-compatible workflows, item-level states, preparation timing, ready/served handoff and production exceptions.

### Timed services and gaming
Generic timed-resource sessions plus gaming-specific device/controller/player/rate/package/booking behavior.

### Wi-Fi
Router adapters beginning with MikroTik, hotspot/package policy, vouchers, quotas, time/data/speed constraints, issuance/printing/QR, status/revocation and captive-portal integration contracts.

### Inventory and procurement
Ingredients, sellable stock, packaging, recipes/BOM, yield, stock ledger, branch stores, counts, transfers, purchase orders, receiving, suppliers, waste and COGS.

### Staff operations
Operational staff profiles, roles, shift assignment, attendance hooks, station/table assignment, permissions, tips/commissions where enabled and operational metrics.

### Customer growth
Customer profiles, loyalty, stamps/points, memberships, bundles/credits, gift value, customer notes/preferences and consent-aware communication preferences.

### Menuza integration
Canonical menu publication, branch availability, QR context, customer order intake, order status callbacks and online payment/fulfillment contracts.

### Fiscal and controls
Egypt ETA eReceipt adapter, document sequencing/identity, fiscal queue/retry/reconciliation, expense/petty cash, audit and close-of-day controls.

### Intelligence
Owner control center, live venue status, sales/margin/throughput/utilization/waste/leakage analytics and later anomaly/AI insight hooks.

## Explicit non-goals for first production frontier

- full payroll/accounting ERP;
- manufacturing/MRP beyond café recipes and simple prep/yield;
- owning Menuza's public-menu/theme/CMS product;
- reproducing a full restaurant product inside Bunova when shared contracts can be used;
- native delivery-fleet optimization as a first release requirement;
- marketplace aggregator integrations before core order/fiscal/stock truth stabilizes;
- generalized hotel/PMS/banquet management;
- speculative AI chatbot features without reliable operational data.

## Expansion-safe but not assumed

Billiards, coworking seats, meeting rooms, event packages, NFC membership, SoftPOS/payment-terminal adapters, IoT device power control, additional router vendors, franchise royalties and advanced workforce/payroll may be added behind existing domain boundaries.
