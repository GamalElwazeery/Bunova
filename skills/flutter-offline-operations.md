# Skill: Flutter Offline Operations

## Use when
Building POS/waiter/production/gaming operational Flutter surfaces.

## Method

- Treat local database and outbox as part of product correctness.
- Separate local projection, command creation, sync transport and reconciliation UI.
- Make device/branch/user context explicit.
- Persist open shift/cart/order/session state needed after app/process restart.
- Use globally unique offline-safe IDs.
- Show offline/stale/sync/conflict status without blocking legitimate allowed work.
- Never optimistically display an external payment as final unless provider semantics prove it.
- Test killed app/restart, duplicate replay, disconnect/reconnect and stale config.

## UX
Immediate local feedback is allowed; distinguish local pending from server/fiscal/provider confirmed states clearly.
