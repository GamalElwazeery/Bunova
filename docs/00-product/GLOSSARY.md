# Bunova Canonical Glossary

Use these terms consistently in plans, code, APIs and UX.

- **Organization** — top-level tenant/customer account.
- **Brand** — optional commercial brand under an organization.
- **Branch** — physical operational location.
- **Register** — financial POS endpoint/context associated with a device.
- **Device** — registered hardware/app instance.
- **Capability** — independently enabled product function with declared dependencies.
- **Preset** — onboarding configuration that selects capabilities; not an architecture fork.
- **Catalog Item/Product** — sellable commercial item.
- **Variant** — sellable product variation such as size.
- **Modifier** — selectable option/add-on/replacement on a line.
- **Order** — accepted customer fulfillment intent.
- **Bill** — financial charge aggregation for one or more billable lines.
- **Billable Line** — canonical financial representation of product/service/time/access value.
- **Tender** — payment method/value source.
- **Venue Resource** — table, room/cabin or other physical service location.
- **Venue Session** — live customer visit/service context on a venue resource.
- **Timed Resource** — exclusive/shared resource whose usage may be priced by elapsed time.
- **Timed Session** — measured usage of a timed resource.
- **Production Station** — bar, kitchen, shisha or preparation destination.
- **Production Item** — station-level preparation unit derived from an order line.
- **Pickup Token** — human-facing branch/day collection reference; not the order primary key.
- **Wi-Fi Entitlement** — right to guest access under time/data/speed/device rules.
- **Voucher** — credential/token that grants an entitlement.
- **Stock Ledger** — immutable-oriented sequence of stock movements.
- **Fiscal Document** — jurisdictional receipt/return representation submitted/recorded through fiscal domain.
- **Menuza** — external/related customer-facing digital menu and ordering system.

Avoid overloaded use of "session" without qualifier when venue, timed, authentication or Wi-Fi session could be meant.
