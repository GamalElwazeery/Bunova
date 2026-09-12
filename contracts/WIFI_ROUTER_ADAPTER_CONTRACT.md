# Wi-Fi Router Adapter Contract

## Core abstraction

Router adapters translate Bunova Wi-Fi entitlements into provider/router-specific hotspot behavior.

## Capabilities

Adapter advertises supported features: time limit, data quota, speed shaping, concurrent device limit, credential generation, active-session query, disconnect/revoke, usage query and health check.

## Operations

- `healthCheck`
- `provisionEntitlement`
- `revokeEntitlement`
- `disconnectSession` where supported
- `getEntitlementStatus`
- `getUsage` where supported
- `reconcile`/list normalized active state where supported

## Idempotency

Provision uses stable external identity. Repeated request for same Bunova entitlement must not generate multiple paid access records unintentionally.

## Failure semantics

Network/auth/configuration/router error is distinguished from business validation. Paid entitlement stays `provisioning_failed/pending` until retry/refund/reconciliation; never fake activation.

## Security

Credentials are encrypted/secret-managed, least privilege, never displayed to ordinary staff. TLS/VPN/local network strategy is chosen per deployment; insecure default management exposure to public internet is prohibited.

## MikroTik

First implementation targets MikroTik through this contract; no MikroTik-specific fields are allowed in core billing/customer models.
