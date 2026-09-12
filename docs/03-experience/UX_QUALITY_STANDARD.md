# Premium UX Quality Standard

## Definition

Premium UX in Bunova means **fast, predictable, information-rich without clutter, resilient under operational stress, and complete across states**.

A glossy generic dashboard with weak workflows is not premium.

## Required states

Every applicable feature designs and implements: loading/skeleton, empty-first-use, empty-filter, success, validation error, system error, offline, stale data, permission denied, conflict/reconciliation, destructive confirmation, partial completion and retry/recovery.

## High-frequency workflow budgets

Plan and test click/tap count and latency for counter sale, add common modifier, exact-cash sale, card sale, repeat order, open table, add table order, start/end gaming session, issue Wi-Fi voucher, mark production ready and request bill.

Common counter actions should avoid modal chains and unnecessary confirmations. Risky/destructive actions can require stronger friction.

## Information hierarchy

Operational surface must prioritize current action, queue/amount/time/status and exceptions. Secondary analytics/configuration stays out of cashier/barista views.

## Cards vs tables

Choose based on task density. Operational entities often benefit from responsive cards/tiles; administrative bulk comparison may legitimately use dense data tables with mobile alternatives. Never ban tables dogmatically.

## Forms

Use progressive disclosure, sensible defaults, inline help and validation. Configuration forms show business consequences of options (rounding, offline policy, tax, stock consumption) rather than obscure switches.

## Feedback

Every command shows immediate local intent and authoritative final state. External/queued operations use pending/retry states rather than fake success.

## Destructive/sensitive UX

Refund, void, shift override, duration edit, stock adjustment and fiscal correction show clear impact, permission reason and reference to original record.

## Empty demo content

Production builds never use fake revenue/orders/customers to make dashboards look populated. Empty states teach setup and next action.
