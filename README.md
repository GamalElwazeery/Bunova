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
- Wi-Fi vouchers, quotas, batch cards, and captive access
- Shisha operations
- Inventory, recipes, waste, and procurement
- Staff operations and optional Payroll-Lite workforce costing
- Loyalty, promotions, memberships, bundles, and gift value
- Reservations
- Digital menu and online ordering through Menuza
- Egypt fiscal/eReceipt compliance
- Multi-branch operations, analytics, and owner control

## Product boundaries

Bunova owns café operational truth: branch/device configuration, orders, sessions, bills, payments, cash shifts, timed resources, gaming, Wi-Fi, café production orchestration, inventory consumption, operational staff workflows, and café analytics.

The existing Restaurant System remains the authority for restaurant-specific depth and reusable F&B patterns such as advanced kitchen/KDS, recipes, table-service, and restaurant workflows. Reuse must happen through explicit contracts or extracted shared capabilities rather than copy/paste.

Menuza remains the customer-facing demand layer for digital menu, QR-context ordering, public storefront, and online ordering. Bunova is the operational system of record that accepts, prices, routes, fulfills, pays, stocks, and fiscally records those orders.

## Repository state

This repository remains **planning-first**. The native Bunova planning corpus and `P00-GATE` are complete. Product implementation is still intentionally blocked.

The repository-side `K00` AI-ExecutionKit integration is installed against exact source pin `5a9eda4ab2159f93cca9867b492fe9ceb65ce261` (VERSION 3.0.0) at `.executionkit/runtime/kit`. Bunova-native authorities are preserved and routed through Agent OS; the Antigravity workspace plugin, hooks, MCP configuration and native role/skill adapters are present; Execution/Agent/Content/SEO/Audit/Test/Launch plus cross-cutting State OS and Premium Experience are reconciled at repository/configuration level.

`K00` is **not yet activated**. A real initialized Antigravity/runtime-capable checkout must still materialize the private submodule, regenerate the native task projection, run the applicable validators/routing checks, prove State checkpoint → rehydrate → Amnesia, and prove Antigravity plugin/hook/MCP behavior. Only after independent `K00-019` acceptance and `K00-GATE` activation may `P01` product implementation begin. Static repository presence is never treated as host/runtime PASS.

## Canonical entry points

- [`TODO.md`](TODO.md) — sole mutable execution/backlog authority and normal owner/agent entrypoint.
- [`AGENTS.md`](AGENTS.md) — mandatory operating instructions for any coding/planning agent.
- [`docs/executionkit/ADOPTION.md`](docs/executionkit/ADOPTION.md) — exact ExecutionKit adoption/preservation authority.
- [`scripts/executionkit/README.md`](scripts/executionkit/README.md) — pinned runtime/host acceptance runbook; commands are agent-owned diagnostics, not a separate owner checklist.
- [`docs/executionkit/TOOLING_STATUS.md`](docs/executionkit/TOOLING_STATUS.md) — static vs host/runtime evidence boundary.
- [`docs/PLANNING_INDEX.md`](docs/PLANNING_INDEX.md) — navigation across the planning corpus.
- [`docs/00-product/PRODUCT_VISION.md`](docs/00-product/PRODUCT_VISION.md) — product intent and success model.
- [`docs/01-architecture/DOMAIN_MAP.md`](docs/01-architecture/DOMAIN_MAP.md) — domain and ownership map.
- [`docs/04-delivery/PHASE_PLAN.md`](docs/04-delivery/PHASE_PLAN.md) — P00 → K00 → P01..P21 delivery sequence.
- [`docs/05-governance/TRACEABILITY_CONTRACT.md`](docs/05-governance/TRACEABILITY_CONTRACT.md) — plan → task → implementation → evidence contract.
- [`docs/05-governance/NATIVE_PLANNING_AUDIT.md`](docs/05-governance/NATIVE_PLANNING_AUDIT.md) — native-planning closure evidence and corrections.

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
5. Financial history, fiscal history, stock movements, timed usage, stored value, and audit history are never modeled as casually mutable CRUD.
6. Offline operation is a first-class product property for operational surfaces, not a later fallback.
7. Shared functionality with Restaurant/Menuza must use explicit ownership and integration contracts; no silent duplication.
8. Premium UX means operational speed, clarity, recovery, accessibility, RTL correctness, and state completeness—not decoration.
9. No demo/mock/placeholder data may survive production readiness unless explicitly marked as a test fixture.
10. Each phase closes only with evidence, tests, audit findings resolved, documentation synchronized, and its exit gate passed.

## Status

**Native planning is complete. Repository-side ExecutionKit integration is installed and statically reconciled. Current frontier: K00 host/runtime validation → independent audit → activation. Product implementation remains blocked until `K00-GATE` closes.**
