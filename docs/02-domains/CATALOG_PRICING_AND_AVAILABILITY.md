# Catalog, Pricing and Availability Domain

## Goal

Provide one canonical sellable catalog that can serve POS, waiter, production routing, Menuza publication, inventory recipes and analytics without duplicating item definitions per channel.

## Core entities

- Category / collection
- Product
- Variant / size
- Modifier group
- Modifier option
- Unit and unit conversion
- Tax classification
- Price list / price rule
- Branch override
- Availability rule
- Product media
- Channel visibility
- Production routing metadata
- Recipe linkage

## Product model rules

A product represents the commercial concept (for example Latte). Size/temperature/milk/extra-shot behavior should use variants/modifiers when semantically appropriate rather than exploding every combination into separate products.

Modifier groups define required/optional selection, min/max choice, defaults, price delta, inventory/recipe effect and allowed variants/channels.

Every item/variant/modifier has stable IDs; localized labels are separate from identity.

## Pricing

Pricing supports:

- base product/variant price;
- modifier price deltas;
- organization defaults with branch override;
- channel-specific pricing only where explicitly enabled;
- scheduled/daypart pricing;
- customer/membership entitlements applied through billing rules rather than overwriting catalog price;
- tax-inclusive or tax-exclusive configuration according to jurisdiction/business policy;
- historical snapshotting at order/bill time.

Price-rule precedence must be deterministic and explainable in UI/audit.

## Availability

Availability is distinct from active/deleted state.

Sources include manual sold-out, schedule/daypart, branch capability, inventory advisory/blocking policy, production station outage and channel visibility.

The system must explain why an item is unavailable and publish that state to Menuza/operational devices quickly. Inventory-based availability must not silently delete products.

## Catalog publication

Menuza receives versioned publication payloads/read models including category/order, localized text, media references, variants/modifiers, prices, allergens/tags if enabled, branch/channel availability and stable IDs. Menuza presentation settings remain outside this domain.

## Acceptance risks

Combinatorial modifiers, branch overrides, conflicting schedule rules, tax changes, stale offline pricing and Menuza synchronization all require deterministic tests.
