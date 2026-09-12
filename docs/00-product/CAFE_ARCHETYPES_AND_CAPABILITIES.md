# Café Archetypes and Capability Model

## Architecture rule

**Presets configure capabilities; presets never fork the product architecture.**

An organization may alter capabilities after onboarding. Data and workflows must remain valid as capabilities are enabled/disabled; disabling a capability hides/blocks new use but must not erase historical records.

## Core capabilities

`core.organization`, `core.branch`, `core.device`, `core.identity`, `core.permissions`, `catalog`, `pos`, `orders`, `billing`, `payments`, `cash-shifts`, `audit`, `reporting`.

## Optional capability families

- `venue.floors`, `venue.tables`, `venue.rooms`, `venue.sessions`
- `service.waiter`
- `production.bar`, `production.kitchen`, `production.shisha`, `production.kds`
- `timed-resources`
- `gaming`
- `wifi`
- `inventory`, `procurement`, `recipes`, `waste`
- `menuza.menu`, `menuza.qr-ordering`, `menuza.online-ordering`
- `delivery-entry`
- `reservations`
- `loyalty`, `memberships`, `bundles`, `gift-value`
- `fiscal.egypt`
- `analytics.advanced`

## Recommended presets

### Coffee Cart / Mobile Kiosk
Core + catalog + POS + takeaway + bar production + simple stock + offline device mode. Optional Menuza pickup.

### Takeaway Coffee Kiosk
Core + POS + takeaway + bar production + inventory/recipes + Menuza digital menu/online pickup.

### Coffee Shop
Core + floors/tables/sessions + waiter + bar + inventory + Menuza QR + loyalty.

### Traditional Café
Coffee Shop + shisha + Wi-Fi + optional gaming + kitchen + richer session billing.

### Café & Restaurant
Coffee Shop + kitchen/KDS + restaurant interoperability + reservations + delivery-entry + richer recipes/procurement.

### Gaming Café
Core + venue sessions + timed resources + gaming + bar + optional kitchen + Wi-Fi + memberships + packages + reservations.

### Internet / Coworking Café
Core + timed resources + Wi-Fi + tables/rooms + memberships + bar + reservations.

### Multi-branch Chain
Any operational preset + centralized catalog/pricing rules, branch overrides, roles, consolidated reporting, stock transfer and fleet/device governance.

## Capability dependency examples

- `gaming` depends on `timed-resources`, `billing`, and `venue.sessions` or an equivalent session context.
- `production.kitchen` depends on `orders` and station routing.
- `menuza.qr-ordering` depends on Menuza integration, catalog publication and venue QR context when table-bound.
- `fiscal.egypt` depends on finalized bill/payment/fiscal identity primitives.
- `recipes` depends on inventory units/conversions and catalog sellable items.

The exact dependency graph must be machine-readable during implementation readiness and enforced by configuration validation.
