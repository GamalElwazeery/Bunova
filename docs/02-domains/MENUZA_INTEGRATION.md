# Menuza Integration Domain

## Principle

Menuza is the customer-experience layer; Bunova is the café operational source of truth. Integration is contract-based with explicit versioning and reconciliation.

## Catalog publication Bunova -> Menuza

Publish organization/brand/branch context, categories, products, variants, modifiers, localized content fields designated as operational catalog truth, prices, taxes/display data where needed, channel visibility, sold-out/availability state and stable IDs.

Publication uses version/checkpoint so Menuza can detect stale state and request/rebuild a branch snapshot.

## QR context

Bunova owns venue/resource identities. QR payload/alias resolves to branch plus optional floor/table/room/gaming/session context. Tokens must be signed/opaque enough to prevent customer mutation of privileged IDs.

## Order submission Menuza -> Bunova

Payload includes Menuza order ID/idempotency key, branch/channel, QR/service context, customer/contact/consent fields permitted, cart line stable IDs + selected modifiers, quoted prices/version, delivery/pickup info if relevant and payment reference/status if payment occurred externally.

Bunova validates current price/availability/context and returns accepted/rejected/price-change semantics; duplicate submission returns the original result.

## Status Bunova -> Menuza

Expose normalized operational status and pickup/table context without leaking internal-only workflow. Status updates are retryable/idempotent; Menuza can reconcile by query if callbacks fail.

## Online payment

A Menuza payment provider result is not trusted as settled without verified provider/reference contract. Ownership of payment creation/verification must be explicit per integration mode.

## Failure/reconciliation

Catalog drift, duplicate order, timeout-after-accept, stale price, unavailable item, closed branch, invalid QR, partial callback failure and payment ambiguity all have planned test fixtures. Admin exposes reconciliation rather than forcing database edits.
