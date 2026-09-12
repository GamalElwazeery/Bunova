# Security and Privacy Rules

- Server-side authorization is mandatory for privileged commands.
- Tenant/branch scoping is explicit and covered by negative tests.
- Secrets never enter Git, logs, screenshots or audit payloads.
- Webhooks/provider callbacks are authenticated and replay-safe.
- Device credentials use platform secure storage and can be revoked.
- Sensitive exports/configuration changes are permissioned/audited.
- Collect minimum customer data; marketing consent is separate.
- Support tooling is least-privileged and diagnostics are redacted.
- Direct production database editing is not an accepted routine support workflow.
