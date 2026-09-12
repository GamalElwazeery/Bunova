# Skill: Integration Contracts

## Use when
Changing Menuza, payment, ETA, MikroTik/router, notification or Restaurant interoperability.

## Procedure

1. Confirm owner/source-of-truth on both sides.
2. Define versioned request/response/event schema.
3. Define auth/signature/secret handling.
4. Define idempotency and duplicate handling.
5. Define timeout-before/after-side-effect ambiguity.
6. Define retry/backoff and dead-letter/reconciliation.
7. Define observability/support fields.
8. Build provider fake/fixture and contract tests.
9. Keep provider-specific data at adapter boundary.

No external integration is accepted with only a success-path API call.
