# ADR-004: Offline-first Operational Clients

**Status:** Accepted

## Decision
POS and selected operational Flutter workflows use local durable state + outbox/synchronization rather than treating offline as a later cache feature.

## Why
Café revenue cannot stop for ordinary internet outage; gaming/session/cash state must survive app restart.

## Consequences
Global IDs, idempotency, conflict policy, local schema migration, device identity, reconciliation UI and offline tests are foundational work, not polish.
