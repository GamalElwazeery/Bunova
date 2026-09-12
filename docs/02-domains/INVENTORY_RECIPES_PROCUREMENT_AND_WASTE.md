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

## Supplier operational dues

Received supplier invoices may create an operational payable amount with due date/terms. Supplier settlements/payments are append-oriented movements referencing one or more invoices/receipts; partial payment and credit/return adjustments remain traceable. Supplier statement shows opening operational balance, invoices/credits, payments and closing balance for the selected scope.

This feature exists so the café can answer “إحنا علينا كام للمورد؟” from operations without pretending Bunova is a statutory accounts-payable/general-ledger suite. Manual adjustment/write-off requires explicit permission, reason and audit, and launch-market accounting/tax treatment must be validated.

## Costing

Maintain moving/weighted or configured costing policy centrally. Product theoretical COGS uses recipe and cost snapshots/projections; reports distinguish theoretical versus waste/variance effects.
