# ADR-003: Modular Monolith First

**Status:** Accepted

## Decision
Start Bunova backend as a modular Laravel monolith with strict bounded contexts and adapter/event seams rather than independent microservices.

## Why
Core café operations have strong transactional relationships and the project benefits from simpler deployment while product truth stabilizes. Explicit modules/contracts preserve future extraction options.

## Consequences
No cross-module free-for-all: ownership and APIs/services/events remain explicit. External integrations/queues can be isolated operationally without forcing service proliferation.
