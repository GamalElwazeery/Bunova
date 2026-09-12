# Unified Billing Contract

## Contract purpose

All Bunova domains that generate chargeable value MUST express that value through the canonical billing boundary. No domain may invent an independent checkout/payment truth.

## Required billable source types

- catalog product/variant/modifier sale;
- timed-resource rated usage;
- Wi-Fi/access package sale;
- shisha/service charge;
- room/service fee;
- bundle/package allocation;
- permitted adjustment/charge/discount/tip representation.

## Source-to-bill requirements

A billable source supplies stable source type/id, organization/branch, display snapshot, quantity/measure, pricing/rating snapshot, tax classification, discount eligibility, service context and correlation identifiers.

Billing returns/stores canonical line totals and allocations. Source domains MUST NOT recalculate finalized bill totals later using current configuration.

## Invariants

1. Money uses canonical precision; no binary floating point.
2. Finalized lines are historically reproducible/explainable.
3. Payment allocation equals paid/refunded amount exactly.
4. Splits/merges preserve original-source traceability.
5. Refunds reference original finalized financial facts.
6. Rate/price/tax changes never rewrite settled history.
7. A domain event replay cannot create duplicate billable lines if idempotency identity is the same.

## Prohibited patterns

- Gaming session writes directly to payment table.
- Wi-Fi router success creates revenue without a bill.
- Product refund deletes original sale.
- Current product price is joined into historical receipt rendering as the financial source.
- Client-supplied totals are trusted without server-side canonical calculation.
