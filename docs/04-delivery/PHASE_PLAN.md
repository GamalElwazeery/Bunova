# Bunova Delivery Phase Plan

This plan defines the product sequence. `TODO.md` is the sole mutable execution authority and contains the executable task detail.

## P00 — Repository truth and native planning closure
Freeze product vocabulary, domain ownership, integration boundaries, architecture decisions, task semantics, acceptance standards and native implementation-readiness evidence. No product implementation is authorized by closing P00.

## K00 — AI-ExecutionKit deep integration gate
Dedicated next-round integration against the **current live AI-ExecutionKit**, using its supported installer and current system authorities. Preserve Bunova-native planning, merge agent/skill/rule/workflow/contract authority deeply, keep `TODO.md` as the only mutable backlog, register and route every applicable live kit system, and verify the integration independently. Product implementation remains blocked until K00 closes.

## P01 — Engineering foundation
Laravel/backend and Flutter workspace foundations; environments; organization/branch tenancy; auth; roles; device/register identity; capability configuration; observability; localization; CI baseline.

## P02 — Catalog, pricing and menu truth
Categories, items, variants, modifier groups, units, taxes, pricing rules, branch overrides, availability, media and Menuza publication contract.

## P03 — POS, orders and unified billing
Counter/takeaway flows, cart/order lifecycle, billable-line abstraction, discounts, service charges, minimum/cover charge hooks, tips, cash/card/mixed tender, refunds/voids, receipts, cash drawers and shifts.

## P04 — Venue, floors, tables and service sessions
Visual floor structure, tables/rooms, occupancy/session lifecycle, transfers/merges/splits, waiter assignment, service requests, configurable minimum-spend/cover policies and session-aware billing.

## P05 — Production OS
Stations, routing, bar/barista workflow, kitchen/KDS interoperability, shisha-production hooks, preparation timers, ready/serve flow, re-fire/remake/cancel and production analytics.

## P06 — Inventory, recipes and procurement
Stock ledger, stores, units/conversions, recipes/BOM, automatic consumption, waste, counts, transfers, suppliers, PO/receiving and COGS.

## P07 — Staff operations
Operational staff, shifts/attendance hooks, table/station assignment, permissions, tips/commissions, staff consumption, optional workforce-cost/payroll-lite controls (wage basis, advances, deductions, overtime/payout evidence) and operational performance signals. Full HR/payroll compliance ERP remains out of scope.

## P08 — Timed Resource Engine
Generic resource catalog, sessions, pause/resume/transfer, rate plans, rounding/minimum rules, packages, reservation linkage and unified bill integration.

## P09 — Gaming OS
Console/PC inventory, player/controller/room semantics, gaming rate models, packages/memberships, bookings, device-state workflows and order attachment.

## P10 — Wi-Fi OS
Router adapter contract, MikroTik integration, hotspot packages, individual/batch voucher issuance and printable cards, time/data/speed quotas, captive portal handshake, guest entitlement, revocation and outage recovery.

## P11 — Shisha and specialized café service
Shisha catalog/modifiers/recipes, station workflow, coal/service requests, stock/costing and table/session integration.

## P12 — Menuza integration and online demand
Catalog sync/publication, QR context, table online ordering, pickup/delivery entry, availability, pricing, payment/status callbacks, idempotency and recovery/reconciliation.

## P13 — Customer, loyalty, memberships and commercial packages
Profiles/consent, points/stamps, rewards, coupons/promotions, prepaid bundles/credits, memberships, gift value, café/gaming combinations and entitlement consumption.

## P14 — Reservations and customer scheduling
Tables, rooms and timed-resource reservations, deposits, capacity/conflict logic, no-show/cancel rules and arrival/session conversion.

## P15 — Fiscal, expenses and financial controls
ETA eReceipt adapter, fiscal queue/retry/reconciliation, cash expenses/petty cash, end-of-day controls, settlement and immutable audit evidence.

## P16 — Owner control center and analytics
Live Café operations, revenue/margin, channel/station/resource utilization, stock/waste, shift leakage, staff/service metrics, customer retention and branch comparison.

## P17 — Offline-first synchronization and resilience hardening
Local POS data, event/outbox/inbox, conflict policy, ordering/session/shift continuity, reconnect reconciliation, device-loss handling and chaos/offline tests.

## P18 — Hardware, peripheral and integration hardening
Thermal printers, cash drawers, barcode scanners, customer displays, KDS/bar screens, router/device health and payment-provider adapter boundaries.

## P19 — Premium UX, accessibility, localization and performance
Arabic/English/RTL, touch/keyboard/device ergonomics, responsive admin, all application states, design-system consistency, performance budgets and visual/interaction audit.

## P20 — Security, audit, full test and operational acceptance
Threat-model closure, tenant isolation, authorization matrix, financial/fiscal abuse cases, penetration-oriented checks, full regression, data integrity and disaster/recovery evidence.

## P21 — Onboarding, migration, documentation and launch
Setup wizard, templates, data import, training/help, support/diagnostics, production configuration, backup/restore drills, release checklist, rollout/rollback and launch evidence.

## Phase gate rule

A phase is not complete merely because its happy-path implementation exists. Each phase gate requires all scoped tasks complete, relevant automated/manual tests passing, audit findings resolved or explicitly accepted, documentation/contracts synchronized, and evidence recorded by the canonical task system.
