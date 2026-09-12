# Data Ownership and Historical Integrity

## Operational system of record

Bunova is authoritative for accepted café orders, venue/timed sessions, bills, payment records, cash shifts, production state, stock ledger, Wi-Fi entitlement issuance and café operational configuration.

Menuza remains authoritative for its customer-facing presentation/CMS configuration, but Bunova owns whether an order was operationally accepted/fulfilled/paid and the branch availability data it publishes.

The Restaurant System remains authoritative for restaurant-specific product behavior not extracted/shared. Any shared data exchange must identify which side owns writes.

## Append/immutable-oriented records

The following are never treated as casually editable rows after finalization:

- finalized bill snapshots;
- payment/refund allocations;
- cash movements and shift close evidence;
- stock ledger movements;
- fiscal submission/response history;
- timed-resource usage segments after billing;
- loyalty/prepaid/gift ledger movements;
- sensitive audit events.

Corrections use compensating or versioned records.

## Master vs snapshot data

Catalog/customer/resource master data can change. Transactions preserve snapshots of names, prices, taxes, modifiers, rate plans and other facts needed to explain history.

## Deletion

Prefer archive/deactivate for referenced master data. Hard deletion is limited to unreferenced drafts/test data under explicit rules. Tenant deletion/export/retention must later define privacy and legal requirements without corrupting financial/fiscal history.
