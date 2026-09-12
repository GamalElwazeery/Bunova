# Fiscal Adapter Contract

## Purpose

Separate stable Bunova financial truth from jurisdiction/provider-specific fiscal submission rules.

## Input

Adapter receives immutable normalized fiscal snapshot referencing finalized Bunova bill/refund: seller/branch/device fiscal identity, transaction/return linkage, item snapshots/codes, quantities, discounts/charges, tax details, payment summary, timestamps and canonical fiscal identity/idempotency fields.

## Output/status

Adapter returns normalized submission result with status, external UUID/reference, validation errors, retry classification, raw response reference and reconciliation hints.

## Rules

- Mapping does not mutate original bill.
- Submission is durable and retryable.
- Duplicate/retry behavior follows provider requirements and Bunova stable identity.
- Rejection is visible and actionable; sale remains financially recorded.
- Return/correction references original fiscal document as required.
- API/schema/legal requirements are revalidated against official current documentation before implementation and release.

## Egypt ETA first adapter

The initial adapter targets ETA eReceipt. Registered POS identity, item coding, UUID/previous relationships, document/return type and security credentials are isolated in adapter/fiscal configuration rather than scattered throughout POS code.
