# Restaurant System Interoperability Contract

## Goal

Reuse mature F&B behavior without coupling Bunova to another product's database or cloning logic invisibly.

## Reuse decision order

1. Shared versioned package/domain library when both products can depend on it safely.
2. Versioned API/service contract when separate deployment/ownership is required.
3. Shared normative specification + independent implementations only when justified.

Copy/paste is not a reuse strategy.

## Candidate shared domains

Production/KDS state model, recipes/units, table-service concepts, restaurant reservations, fulfillment/delivery-entry primitives and catalog modifier conventions.

## Boundary requirements

For every shared/reused capability document:

- canonical owner;
- version/change policy;
- data ownership;
- API/event/package contract;
- failure behavior;
- migration compatibility;
- test suite/contract tests.

## Bunova-specific invariants

Restaurant reuse MUST NOT force Bunova to model gaming, timed resources, Wi-Fi or café session billing through restaurant-specific abstractions. Shared F&B primitives adapt into Bunova's canonical order/billing/session domains.
