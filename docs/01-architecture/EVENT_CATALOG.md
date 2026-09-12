# Domain and Integration Event Catalog

This is a planning catalog; schemas are versioned contracts before implementation.

## Catalog
- `catalog.item.created.v1`
- `catalog.item.updated.v1`
- `catalog.item.availability_changed.v1`
- `catalog.price_changed.v1`
- `catalog.publication_requested.v1`

## Orders / billing / payments
- `order.submitted.v1`
- `order.accepted.v1`
- `order.rejected.v1`
- `order.cancelled.v1`
- `bill.finalized.v1`
- `payment.recorded.v1`
- `payment.refunded.v1`

## Venue / production
- `venue.session.opened.v1`
- `venue.session.transferred.v1`
- `venue.session.closed.v1`
- `production.item.queued.v1`
- `production.item.started.v1`
- `production.item.ready.v1`
- `production.item.served.v1`

## Timed resources / gaming
- `timed_session.started.v1`
- `timed_session.paused.v1`
- `timed_session.resumed.v1`
- `timed_session.transferred.v1`
- `timed_session.ended.v1`
- `timed_session.rated.v1`

## Wi-Fi
- `wifi.voucher.issued.v1`
- `wifi.entitlement.activated.v1`
- `wifi.entitlement.exhausted.v1`
- `wifi.entitlement.revoked.v1`

## Inventory
- `stock.movement.recorded.v1`
- `stock.count.closed.v1`
- `waste.recorded.v1`
- `purchase.received.v1`

## Customer programs
- `loyalty.movement.recorded.v1`
- `membership.activated.v1`
- `credit_bundle.movement_recorded.v1`
- `gift_value.movement_recorded.v1`

## Fiscal
- `fiscal.document.queued.v1`
- `fiscal.document.submitted.v1`
- `fiscal.document.accepted.v1`
- `fiscal.document.rejected.v1`
- `fiscal.reconciliation_required.v1`

## Contract rules

Events describe facts, not commands. Schemas carry event ID, occurred time, organization/branch where relevant, aggregate identity/version, schema version and trace/correlation identifiers. Consumers must tolerate duplicate delivery and must not assume global ordering across unrelated aggregates.
