# Offline and Synchronization Architecture

## Product requirement

Operational sales must survive ordinary internet loss. Offline capability is scoped, permissioned and explicit; it is not permission to run every back-office feature disconnected.

## Local-first operational projection

Each authorized POS/operational device stores a branch-scoped projection of:

- device/branch/capability configuration;
- active catalog/pricing/availability needed to sell;
- relevant customer/loyalty subset only where policy permits;
- current local/open shifts;
- locally created orders/bills/payments allowed offline;
- venue/resources/open-session projection needed by the device;
- production projection where the device is used as a station display;
- outbox/inbox and sync checkpoints.

## Command and event model

Offline-created commands/events use globally unique IDs, device ID, organization/branch, local sequence, occurred time, schema version and idempotency key.

Server ingestion validates authorization, tenancy, invariant conflicts and replay. Accepted commands advance authoritative state and return mappings/status. Rejected/conflicted commands remain visible to an operator/manager reconciliation flow—never silently dropped.

## Outbox/inbox

Local writes and outbox enqueue are atomic. Server transactional side effects use a transactional outbox where asynchronous publication is required. Consumers record inbox/idempotency keys before applying repeatable side effects.

## Conflict classes

1. **Commutative** — can merge safely (some independent notes/events).
2. **Server-authoritative configuration** — client refreshes and replays if valid.
3. **Exclusive resource** — table/resource/session collision requires deterministic winner + reconciliation.
4. **Financial** — never auto-merge ambiguous settled money; raise controlled reconciliation.
5. **Stock** — sales may create negative/theoretical variance according to configured offline policy, then reconcile through ledger/count rather than deleting sales.

## Offline payment policy

Cash can normally be recorded offline. Card/wallet behavior depends on payment provider/device capability and cannot be falsely represented as captured. Store provider/offline authorization state explicitly.

## Fiscal policy

Offline sale finalization may enqueue fiscal submission only where legally/configurationally permitted. Fiscal state remains pending until acknowledged; retry/reconciliation is durable and observable.

## Device loss/revocation

Server can revoke device credentials. Locally stored secrets/tokens use platform secure storage. Device registration and sync bootstrap are explicit; stale/revoked devices cannot resume privileged sync indefinitely.

## Required tests

Disconnect mid-cart, mid-payment, mid-session, shift close, production update, duplicate replay, clock skew, two-device exclusive-resource conflict, stale catalog, server rejection, device restart with queued commands, and reconnect convergence.
