# Offline and Realtime Rules

- Realtime sockets accelerate visibility; they are not durable truth.
- Offline-enabled commands are explicitly allowlisted by domain/capability.
- Local state + outbox enqueue is atomic.
- Server ingestion is idempotent and authorization-aware.
- Never silently drop sync conflicts.
- Exclusive resource conflicts surface reconciliation.
- Cash may be recorded offline under policy; external payment success may not be fabricated.
- Pending fiscal/provider work remains visibly pending.
- Local device restart must preserve open shift/order/session/outbox state.
- Device revocation and stale configuration are tested.
