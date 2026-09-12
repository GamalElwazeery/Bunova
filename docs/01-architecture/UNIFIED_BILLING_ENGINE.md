# Unified Billing Engine

## Purpose

Bunova must settle heterogeneous café value on one bill: drinks, food, packaged goods, shisha, gaming time, room time, Wi-Fi access, bundles, service fees and future timed/rental services.

The architecture therefore uses a canonical **Billable Line** abstraction instead of separate checkout engines per module.

## Billable line classes

- `product` — catalog item/variant/modifiers.
- `timed_usage` — rated elapsed usage of a resource/session.
- `access_entitlement` — Wi-Fi or other access package sold as value.
- `service` — shisha/service/cover/other configured service.
- `bundle_component` — value consumed as part of a package where accounting requires allocation.
- `adjustment` — discount, surcharge, service charge, tip, rounding where modeled as explicit lines/elements.

New classes require an ADR and must preserve bill calculation invariants.

## Required snapshots

Each finalized line preserves enough historical detail to reproduce/explain the charge:

- source domain/type/id;
- description and localized label snapshot;
- quantity/usage measure and unit;
- gross unit price or rated amount;
- modifier/add-on breakdown where applicable;
- discount allocations;
- tax category/rate/value snapshot;
- service charge allocation where applicable;
- net/tax/gross totals;
- branch/currency;
- source actor/device/session context.

## Bill states

`draft -> open -> finalizing -> finalized -> partially_paid/paid -> partially_refunded/refunded`

Cancellation/void semantics are separate and constrained by whether financial/fiscal finalization occurred. Finalized fiscal history is corrected through refund/return flows, not destructive deletion.

## Calculation invariants

- Sum(line net + line tax + allocated charges - allocated discounts) reconciles to bill total under the canonical rounding policy.
- Payment allocations reconcile exactly to amount paid/refunded.
- Split bills preserve source-line allocation traceability.
- A line already consumed by a finalized bill cannot silently change when current catalog/rate configuration changes.
- Timed usage rating stores the applied rate-plan snapshot and measured duration basis.

## Split and merge

Support split by item, quantity, person/seat where available, equal amount and custom amount, subject to exact reconciliation. Merge/open-bill transfer must preserve audit and source-context links.

## Tender

Canonical tenders initially include cash, card/external terminal, wallet/QR, bank/manual external, gift/prepaid value, complimentary/house-account where explicitly enabled, and mixed tender. External provider adapters must not leak provider-specific semantics into the core bill model.

## Refunds and corrections

Refunds reference original bill/payment/fiscal facts, support partial quantities/amounts where valid, require configured reason/permission, restore inventory only when business rules say physical goods were returned, and trigger fiscal return/correction workflows when required.
