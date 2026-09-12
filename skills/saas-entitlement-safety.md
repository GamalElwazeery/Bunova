# Skill: SaaS Entitlement and Tenant-Lifecycle Safety

## Use when
Changing plans, trials, subscription billing, commercial entitlements, tenant suspension, platform-admin/support behavior or offline licensing.

## Checklist

- keep Platform Billing separate from Café Billing;
- distinguish commercial entitlement from enabled capability and staff permission;
- version plans/entitlements; preserve historical explanation;
- define branch/device/capability limit measurement and race behavior;
- never delete data on downgrade/suspension;
- protect open shifts/orders/sessions and accepted payment/fiscal reconciliation;
- define signed/versioned offline entitlement projection and bounded grace;
- make provider callbacks idempotent/reconcilable;
- make support/break-glass access purpose-bound, time-bound and audited;
- test downgrade, expired trial, past-due/grace, offline device, limit race, reactivation and provider duplicate/unknown states.
