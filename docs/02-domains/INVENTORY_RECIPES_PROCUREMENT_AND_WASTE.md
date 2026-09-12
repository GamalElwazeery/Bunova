# Inventory, Recipes, Procurement and Waste

## Inventory model

Inventory uses an append-oriented stock ledger; current stock is a projection/reconciliation of movements rather than a freely editable quantity field.

### Stock item classes
- ingredients (beans, milk, fruit, meat);
- resale goods (water/cans/chips);
- packaging (cups/lids/boxes/straws);
- shisha consumables (flavor/coal);
- other tracked consumables.

Durable assets such as consoles/controllers belong to asset/resource management, not consumable stock.

## Units and conversions

Canonical base units plus controlled conversions (kg/g, L/ml, piece, pack). Purchase unit may differ from consumption unit. Conversions must be explicit and test rounding/precision.

## Recipes / BOM

Sellable product/variant/modifier may consume ingredients/packaging. Recipe supports quantity, unit, yield and optional sub-recipe/prep item when enabled.

Modifier choices can add/replace consumption (oat milk replacing regular milk; extra shot adding beans).

## Consumption timing

Branch policy defines when theoretical consumption posts (accepted sale, production start, completion). Once chosen, cancellation/remake/refund behavior must create compensating movements rather than edit history.

## Waste

Waste records item/ingredient, quantity/unit, reason, station, staff, source event optional and cost snapshot. Reasons include spill, remake, expiry, calibration, breakage and manual disposal. Approval thresholds are configurable.

## Counts and variance

Stock count sessions freeze/record expected snapshot, counted quantities, variance, approval and adjustment ledger movements. Never overwrite expected historical stock.

## Transfers

Branch/store transfers have request/dispatch/receive/variance states and create paired ledger facts.

## Procurement

Supplier -> PO -> partial/full receipt -> stock movement -> supplier invoice/reference metadata. Bunova initially tracks operational procurement, not general-ledger accounting.

## Costing

Maintain moving/weighted or configured costing policy centrally. Product theoretical COGS uses recipe and cost snapshots/projections; reports distinguish theoretical versus waste/variance effects.
