# Skill: Laravel Domain Implementation

## Use when
Implementing Bunova backend/web domain behavior.

## Method

1. Start from bounded context and contract, not controller/UI.
2. Define command/use-case boundaries, policies, state transitions and invariants.
3. Model transactional consistency explicitly; external calls leave the core DB transaction through outbox/jobs when needed.
4. Enforce organization/branch scope and authorization server-side.
5. Use value objects/services for money, tax, rate/time and identifiers where they protect invariants.
6. Keep Eloquent persistence concerns from becoming the only domain model for complex money/time/session behavior.
7. Add deterministic tests before wiring presentation for high-risk invariants.
8. Expose versioned API/resources/events according to contracts.

## Avoid
Fat Livewire components/controllers, hidden model-observer side effects across domains, current-master joins for historical financial values, generic CRUD for sensitive transitions and provider-specific logic leaking into core domains.
