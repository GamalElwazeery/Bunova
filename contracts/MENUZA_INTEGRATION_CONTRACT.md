# Menuza Integration Contract

## Ownership

Menuza: public/customer menu and ordering UX.
Bunova: branch operational catalog publication source, availability/pricing truth for accepted sales, order acceptance/fulfillment, billing/payment verification, stock and fiscal truth.

## Catalog contract

Versioned Bunova publication includes stable entity IDs, locale fields, hierarchy/order, variants, modifier constraints, branch/channel prices, relevant tax/display information, media references, availability and publication version/checksum.

Menuza acknowledges publication version. Snapshot rebuild endpoint/process exists for drift recovery.

## QR context contract

Signed/opaque QR context resolves server-side to branch and optional venue resource/session context. Customer input cannot arbitrarily substitute branch/table IDs.

## Order intake contract

Required: Menuza order ID, idempotency key, schema version, branch/channel, cart line stable IDs and selected modifiers, quoted pricing/version, customer/contact fields allowed, service/delivery context and payment reference/state if applicable.

Bunova revalidates price, availability, modifiers, capability, branch status and context. Outcomes include accepted, rejected, repricing_required, invalid_context and duplicate_replay(original result).

## Status contract

Bunova exposes a normalized customer-safe order state and optional pickup token/ETA. Callback delivery is retryable and query reconciliation exists.

## Failure cases that MUST be tested

Duplicate submit, timeout after Bunova accepted, stale price, sold-out item, invalid QR, closed branch, Menuza callback outage, Bunova outage, online payment success with order timeout, ambiguous provider state and catalog version drift.
