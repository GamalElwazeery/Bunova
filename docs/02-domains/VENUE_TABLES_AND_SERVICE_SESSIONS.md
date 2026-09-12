# Venue, Floors, Tables and Service Sessions

## Venue structure

Organization -> Branch -> Floor/Zone -> Service resource (table, room, cabin or other venue resource).

Visual floor-plan coordinates are presentation metadata; operational identity/status is independent so floor layouts can change without destroying history.

## Table/resource status

At minimum: `available`, `reserved`, `occupied`, `bill_requested`, `cleaning`, `blocked/out_of_service` with type-specific applicability.

Status is derived where possible from reservation/session facts; manual overrides are permissioned/audited.

## Service session

Opening a venue session records branch/resource, opened-by staff/device, guest count optional, customer optional and service assignment. Multiple orders may attach over time. Timed-resource sessions may also attach to the venue session.

Closing requires all chargeable usage represented on bill(s), settlement policy satisfied, and required production/service items resolved.

## Minimum-spend / cover policy

A branch/zone/table/room/session may optionally enforce a configured cover or minimum-spend rule, including per-session or per-guest basis and schedule/daypart applicability. The rule must be transparent before settlement and represented through the canonical Billing domain as an explicit service/adjustment outcome. It must never silently rewrite product prices or create an unexplained total.

Manager waiver/override requires permission, reason and audit. Online/Menuza customer contexts must be able to receive any customer-relevant policy before order confirmation where applicable.

## Transfers and merges

- Transfer table/room while preserving session identity/history.
- Merge two venue sessions only under explicit validation and with source history retained.
- Split guests/bills without rewriting historical order preparation facts.
- Waiter reassignment records history.

## Service requests

Customer/Menuza/QR or staff may create calls such as waiter, bill, water, cleanup or configurable service request. Requests have type, source context, priority, created/acknowledged/completed timestamps and assigned staff where applicable.

## Reservation handoff

A reservation can check in and create a live venue session; reservation identity remains linked for deposit/no-show/analytics.

## Offline contention

Exclusive occupancy conflicts across devices require branch realtime when online and deterministic sync conflict when offline. UI must expose ambiguous/conflicted resource state rather than showing two valid owners invisibly.
