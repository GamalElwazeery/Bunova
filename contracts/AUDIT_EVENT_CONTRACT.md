# Audit Event Contract

## Required fields

- immutable audit event ID;
- organization/branch context;
- occurred/server-recorded time;
- actor identity and actor type;
- device/register/session context when relevant;
- action key;
- target aggregate/entity type and ID;
- correlation/trace ID;
- reason code/free text where policy requires;
- safe structured change metadata or before/after summary;
- source IP/client metadata where appropriate and privacy-safe.

## Mandatory action families

Authentication/admin security changes, role/permission changes, capability/config changes, price overrides, discounts/voids/refunds, complimentary value, cash movement/shift variance approval, stock adjustment/count close, timed-duration/rate override, Wi-Fi/router credential config, fiscal correction/reconciliation, device registration/revocation and sensitive export.

## Security/privacy

Audit is append-oriented, separately permissioned and protected from ordinary mutation. Never store passwords, full payment secrets, router credentials or unnecessary sensitive personal data in audit payload.
