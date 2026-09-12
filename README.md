# Bunova — Café Management OS

Bunova is a modular, offline-capable Café Management Operating System designed for the full range of café businesses: coffee kiosks and carts, drinks-only cafés, traditional cafés, café-restaurants, gaming cafés, internet cafés, lounges, café & bakery concepts, and multi-branch chains.

Bunova is not a reduced restaurant POS. It models café operations as a configurable capability platform where products, table sessions, timed resources, gaming, Wi-Fi access, production stations, and online orders can all converge into one operational and billing truth.

## Product thesis

**One core. Different café shapes. No duplicated operational truth.**

A venue may enable only the capabilities it needs:

- Counter POS / takeaway
- Floor, tables, rooms, and sessions
- Waiter ordering
- Barista / bar production
- Kitchen production and KDS
- Gaming and timed resources
- Wi-Fi vouchers, quotas, and captive access
- Shisha operations
- Inventory, recipes, waste, and procurement
- Loyalty, memberships, bundles, and gift value
- Reservations
- Digital menu and online ordering through Menuza
- Egypt fiscal/eReceipt compliance
- Multi-branch operations, analytics, and owner control

## Product boundaries

Bunova owns café operational truth: branch/device configuration, orders, sessions, bills, payments, cash shifts, timed resources, gaming, Wi-Fi, café production orchestration, inventory consumption, operational staff workflows, and café analytics.

The existing Restaurant System remains the authority for restaurant-specific depth and reusable F&B patterns such as advanced kitchen/KDS, recipes, table-service, and restaurant workflows. Reuse must happen through explicit contracts or extracted shared capabilities rather than copy/paste.

Menuza remains the customer-facing demand layer for digital menu, QR-context ordering, public storefront, and online ordering. Bunova is the operational system of record that accepts, prices, routes, fulfills, pays, stocks, and fiscally records those orders.

## Repository state

This repository begins as a **planning-first repository**. Product implementation must not begin until the canonical planning gates are satisfied.

The AI-ExecutionKit is intentionally **not integrated in this planning round**. Native Bunova planning authorities are being established first; the next integration round will install and deeply merge the kit without replacing or weakening Bunova-specific authority.

## Canonical entry points

- [`TODO.md`](TODO.md) — sole mutable execution/backlog authority.
- [`AGENTS.md`](AGENTS.md) — mandatory operating instructions for any coding/planning agent.
- [`docs/00-product/PRODUCT_VISION.md`](docs/00-product/PRODUCT_VISION.md) — product intent and success model.
- [`docs/01-architecture/DOMAIN_MAP.md`](docs/01-architecture/DOMAIN_MAP.md) — domain and ownership map.
- [`docs/04-delivery/PHASE_PLAN.md`](docs/04-delivery/PHASE_PLAN.md) — delivery sequence and phase gates.
- [`docs/05-governance/TRACEABILITY_CONTRACT.md`](docs/05-governance/TRACEABILITY_CONTRACT.md) — plan → task → implementation → evidence contract.

## Planned implementation baseline

The planning assumes, subject to implementation-time validation:

- Backend / web administration: Laravel + Livewire + Tailwind CSS
- Primary database: PostgreSQL
- Cache / queues / locks: Redis
- Realtime: Laravel Reverb / WebSockets
- POS and operational device apps: Flutter
- Local-first POS persistence: SQLite / Drift
- API style: versioned REST + event contracts where appropriate
- Object storage: S3-compatible
- Egypt fiscal adapter: ETA eReceipt integration behind a provider/domain boundary
- Router integration: MikroTik first, adapter-based expansion later

These are planning defaults, not permission to start coding before readiness gates close.

## Planning principles

1. `TODO.md` is the only mutable backlog and execution queue.
2. Every executable task must be deep enough to execute without inventing product behavior.
3. Every task must trace to product/domain/contract/rule evidence.
4. Presets are onboarding conveniences; capabilities are the architecture.
5. Financial history, fiscal history, stock movements, and audit history are never modeled as casually mutable CRUD.
6. Offline operation is a first-class product property for operational surfaces, not a later fallback.
7. Shared functionality with Restaurant/Menuza must use explicit ownership and integration contracts; no silent duplication.
8. Premium UX means operational speed, clarity, recovery, accessibility, RTL correctness, and state completeness—not decoration.
9. No demo/mock/placeholder data may survive production readiness unless explicitly marked as a test fixture.
10. Each phase closes only with evidence, tests, audit findings resolved, documentation synchronized, and its exit gate passed.

## Status

**Planning in progress. Implementation not authorized yet.**
