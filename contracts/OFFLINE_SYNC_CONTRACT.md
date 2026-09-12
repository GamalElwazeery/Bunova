# Offline Sync Contract

## Scope

Defines how authorized operational clients create and reconcile offline-capable commands/facts.

## Envelope

Every offline command includes:

- globally unique `command_id`;
- stable `idempotency_key`;
- schema version;
- organization and branch identifiers;
- registered device identifier;
- authenticated/local actor context;
- device-local monotonic sequence;
- occurred timestamp plus client clock metadata;
- aggregate type/id and expected version when concurrency matters;
- command payload;
- correlation/trace ID.

## Client atomicity

Local state change and outbox enqueue MUST be atomic. An item is never removed from outbox merely because network send started; only durable server acknowledgement advances it.

## Server ingestion

Server authenticates device/actor, validates tenant/branch, checks idempotency, evaluates authorization/invariants, records accepted command and returns authoritative result/version. Duplicate accepted command returns equivalent prior result.

## Conflict response

Conflict response is structured with class, authoritative state/version and allowed resolution action. Clients MUST NOT silently discard rejected financial/resource-conflict commands.

## Reconciliation

Reconnect process uploads outbox in dependency-safe order, downloads authoritative deltas/checkpoint, resolves/reports conflicts and verifies convergence. Support diagnostics exposes backlog age/count and last successful checkpoint.

## Security

Revoked/stolen devices cannot indefinitely sync queued privileged commands. Credential expiry/revocation and allowed grace policy are explicit.
