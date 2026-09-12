# ADR-002: Unified Billing for Product, Time and Access

**Status:** Accepted

## Decision
All chargeable café value converges through one Billing domain and Billable Line model.

## Why
A single customer visit may include latte, food, PS5 time, room time, shisha and Wi-Fi. Separate checkout engines would break split payment, refunds, fiscalization, shift totals and analytics.

## Consequences
Source domains provide rated/priced source facts; Billing owns finalized financial calculation/snapshot. Payments settle bills, not source modules directly.
