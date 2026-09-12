# Skill: Financial, Time and Stock Integrity

## Use when
Touching bills, payments, discounts, timed rating, stock movements, loyalty/stored value, cash or fiscal flows.

## Checklist

- canonical precision/rounding;
- append/compensating history rather than destructive mutation;
- idempotency and replay behavior;
- deterministic calculation snapshot;
- permission/reason/audit for override;
- negative/zero/boundary quantity and duration cases;
- concurrency tests;
- reconciliation invariant;
- refund/cancel reversal semantics;
- historical report remains stable after master-data change.

A task in these domains is never complete based only on UI/manual happy-path evidence.
