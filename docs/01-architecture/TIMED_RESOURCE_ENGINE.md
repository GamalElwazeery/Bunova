# Timed Resource Engine

## Purpose

One reusable engine prices any finite café resource by elapsed usage: PlayStation, Xbox, PC, VR, billiard table, VIP room, meeting room, coworking seat or future timed service.

Gaming extends this engine; it does not duplicate it.

## Resource model

A timed resource has organization/branch, type, human code/name, zone/room, capacity, operational status, availability status, supported rate plans, optional hardware identity and maintenance state.

Operational states include at minimum `available`, `reserved`, `in_use`, `paused`, `out_of_service`, `maintenance`.

## Session lifecycle

`reserved(optional) -> started -> active <-> paused -> ended -> rated -> billed`

Transfer may move an active session from one compatible resource to another while preserving one logical customer session and an auditable usage-segment history.

## Usage segments

Never calculate a charge only from a mutable `started_at` field. Persist segments with start/end/pause/transfer facts so a dispute can be reconstructed.

## Rate plans

Rate plans may express:

- per-minute/per-hour rate;
- minimum billable duration;
- grace period;
- rounding increment/direction;
- daypart/weekend/holiday applicability;
- player-count multiplier/tiers;
- room/resource tier;
- package/prepaid-credit eligibility;
- promotional override with priority/conflict rules.

The applied rate-plan snapshot is stored when usage is rated.

## Offline behavior

An authorized local device must be able to start/end permitted sessions while temporarily offline. IDs are globally unique; elapsed duration uses device-safe monotonic tracking; queued events sync idempotently. Conflict policy must prevent two devices from legitimately owning the same exclusive resource session without surfacing a reconciliation exception.

## Billing integration

Ending/rating creates one or more canonical `timed_usage` billable lines. A timed session never directly creates a separate payment transaction.

## Safety controls

Starting, comping, editing historical duration, changing applied rate after the fact, or force-ending another operator's session are permissioned and audited. Post-finalization corrections use explicit adjustment/refund flows.
