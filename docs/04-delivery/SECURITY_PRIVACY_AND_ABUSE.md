# Security, Privacy and Abuse Strategy

## Threat model areas

Tenant isolation, staff account compromise, device theft, PIN sharing, privilege escalation, cash/refund fraud, duration manipulation, stock adjustment abuse, router credential exposure, webhook spoofing/replay, payment callback spoofing, fiscal credential leakage, QR tampering, API scraping and privacy leakage.

## Identity/authentication

Web/admin supports strong password and modern MFA/passkey strategy when implementation scope reaches auth design. Operational Flutter device authentication combines registered device trust with staff authentication/quick unlock; biometric can unlock local credential/session but does not replace server-side authorization.

## Authorization

Policy/permission checks live server-side/domain-side. Every sensitive command identifies actor and device. Branch/organization scope is explicit in queries and policies.

## Secrets

Use environment/secret management and platform secure storage; never source-control provider/router/fiscal credentials. Logs redact secrets, payment tokens and sensitive customer data.

## Webhooks/integrations

Verify signatures/credentials, timestamp/replay window where supported, idempotency key and source allowlist/rate limit as appropriate.

## Financial abuse controls

Granular permissions, manager approvals, mandatory reasons, immutable history and exception analytics for refund/void/discount/complimentary/shift variance/duration edit/stock adjustment.

## Privacy

Collect minimum customer information needed. Marketing consent is separate. Define export/deletion/retention flows while preserving legally required transaction/fiscal records. Support diagnostics must redact sensitive information by default.

## Security testing

Automated authorization/tenant negative tests, dependency/static scanning, secret scan, CSRF/XSS/SQLi baseline, API rate tests and targeted manual review of payment/fiscal/sync/router boundaries before launch.
