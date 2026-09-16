# Shared Contracts & Schemas

This package defines the shared, cross-surface contracts, schemas, and projections shared between the Bunova Laravel backend (`backend/`), the Bunova POS/operational clients (`apps/pos/`), and external integrations (such as Menuza, fiscal authorities, and payment providers).

## Purpose & Scope
- **Contract Schemas**: JSON Schema / OpenAPI / AsyncAPI definitions for versioned API envelopes, offline sync event batches, and platform entitlement tokens.
- **Single Source of Truth**: Shared domain data structures and constants (e.g. monetary minor units, capability identifiers, sync mutation types).
- **Decoupling**: Ensures operational clients and cloud backend share identical validation semantics without direct code coupling.

## Relationship to Root `contracts/`
- Root `contracts/` contains high-level architectural and behavioral contracts (e.g. `CAPABILITY_CONFIGURATION_CONTRACT.md`, `PLATFORM_ENTITLEMENT_AND_SUBSCRIPTION_CONTRACT.md`, `OFFLINE_SYNC_CONTRACT.md`).
- `packages/contracts/` houses the concrete machine-verifiable schema definitions, JSON schemas, and shared contract artifacts.
