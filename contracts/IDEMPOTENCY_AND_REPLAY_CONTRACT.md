# Idempotency and Replay Contract

## Where required

Order intake, payment creation/callback, refund, fiscal submission/callback/query reconciliation, Menuza submission, offline command ingestion, stock-consumption event handlers, loyalty/stored-value movement and router entitlement provisioning.

## Key rule

Same idempotency scope + key + semantically same request returns the original accepted result or current representation of that result. Same key with materially different payload is a conflict/security error.

## Storage

Persist key scope, normalized request hash, resulting aggregate/reference, status and retention window appropriate to domain risk.

## Events

Consumers keep processed event/inbox identities transactionally with side effect where possible. At-least-once delivery MUST NOT double-apply money, stock, points, fiscal docs or access entitlements.

## Replay testing

Every high-risk idempotent command has tests sending duplicates concurrently, sequentially and after simulated timeout/crash.
