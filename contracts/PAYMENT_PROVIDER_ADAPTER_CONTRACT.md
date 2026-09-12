# Payment Provider Adapter Contract

## Core interface semantics

Provider adapters expose only supported operations/capabilities such as create/prepare payment, confirm/query status, cancel before capture where supported, refund, partial refund, terminal/SoftPOS pairing or handoff and settlement reference retrieval.

## Canonical statuses

`pending`, `authorized`, `captured/succeeded`, `failed`, `cancelled`, `unknown_reconciliation_required`, `partially_refunded`, `refunded`.

Adapters map provider-specific states to canonical statuses while retaining raw provider reference/payload metadata safely for diagnostics.

## Security

Verify webhook authenticity, replay protection where supported, amount/currency/order reference, provider environment and idempotency. Credentials are secret-managed.

## Unknown state rule

Client timeout or provider timeout MUST NOT be translated directly to failure/success. Query/reconcile before retry when duplicate charge risk exists.

## Offline

Adapter declares explicit offline capabilities. Bunova cannot represent a card payment as captured offline unless the provider/device guarantees an offline authorization/capture workflow and stores its state distinctly.

## Testing

Success, hard decline, timeout-before-provider, timeout-after-provider-success, duplicate webhook, out-of-order webhook, partial refund, full refund, provider outage and reconciliation are mandatory fixtures.
