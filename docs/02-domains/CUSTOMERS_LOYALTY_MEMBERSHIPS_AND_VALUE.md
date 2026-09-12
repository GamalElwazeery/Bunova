# Customers, Loyalty, Memberships and Stored Value

## Customer profile

Minimal profile can start with phone/identifier and optional name; additional contact/demographic data is purpose-limited and consent-aware. Operational preferences may include favorite order or notes with appropriate access controls.

## Loyalty ledger

Points/stamps are ledger movements with source transaction/reason, not only a mutable balance. Rules define earn basis, exclusions, expiry, branch applicability and reward redemption.

Examples: buy 8 coffees get 1; points per spend; double-points daypart; visit stamp.

## Promotions and coupons

Commercial rules may cover happy hour, BOGO/second-item, coffee + bakery, gaming + drink/meal, student/member offers and coupon codes. Rules declare branch/channel/daypart/product/customer applicability, stacking/priority and refund treatment.

Promotions use canonical Billing allocations and must remain explainable at POS/receipt/report level; they never silently overwrite catalog history.

## Memberships

Membership defines validity, recurring/manual renewal policy, included entitlements, discounts, branch/resource eligibility and usage limits.

Examples: Coffee Club, Gamer Pro, Student package.

## Prepaid bundles/credits

Coffee credits, gaming hours or combined bundles maintain entitlement ledger movements. Consumption must be idempotent and reversible via controlled correction when the originating sale/session is refunded.

## Gift value

Gift balance is treated as stored value with issuance/redemption/refund/expiry policy and ledger evidence. Regulatory/accounting treatment must be reviewed before launch.

## Customer tab / house account

When the capability is enabled, approved customers or corporate accounts may consume now and settle later under an explicit credit policy.

A house account defines approved holder, optional credit limit, allowed branches/channels, status and terms. Charges and settlements are ledger movements linked to finalized Bunova bills/payments; staff cannot simply edit a balance. Over-limit, overdue, write-off or manual adjustment requires configured authorization/reason/audit. Statements show opening balance, charges, settlements, refunds/credits and closing balance.

This is controlled operational customer credit, not a general accounts-receivable ERP. Launch-market legal/accounting/tax treatment must be validated before public rollout.

## Recognition at POS

Search/scan/QR identifies customer quickly, shows permitted benefits, and can offer repeat/favorite order. Cashier must see why a benefit applied; automatic rules remain explainable. House-account tender is visible only for authorized eligible customers and within policy.

## Privacy

Customer deletion/export/retention policy distinguishes removable profile data from financial/fiscal history that must be retained legally. Marketing consent is separate from transactional necessity.
