# ADR-006: Restaurant Reuse by Contract, not Copy/Paste

**Status:** Accepted

## Decision
Before implementing overlapping restaurant/F&B behavior, inspect the Restaurant System and choose shared package, API/service contract or normative shared specification. Blind code copying is prohibited.

## Why
Kitchen/KDS/recipe/table logic is expensive and consistency matters, but Bunova has different venue/time/gaming/Wi-Fi concepts and must not become tightly coupled to a restaurant database.

## Consequences
Interop tasks precede overlapping implementation. Shared ownership/version/test obligations are documented explicitly.
