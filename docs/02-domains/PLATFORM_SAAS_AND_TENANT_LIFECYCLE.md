# Bunova Cloud Platform, SaaS and Tenant Lifecycle

## Purpose

Bunova is planned as a sellable multi-tenant product, not merely a deployable café application. Platform-commercial truth must therefore be separated from each café organization's own operational commerce.

**Bunova Platform Billing is not Café Billing.** A café charging its customer for a latte, PS5 time or Wi-Fi uses the Bunova Billing domain. Bunova charging the café organization for its software subscription uses the Platform SaaS domain.

## Platform entities

- Platform Plan
- Plan Version
- Commercial Entitlement
- Trial
- Tenant Subscription
- Subscription Period
- Platform Invoice / Charge Reference
- Platform Payment / Renewal Reference
- Grace / Suspension State
- Platform Operator / Support Role
- Support Access Grant
- Tenant Lifecycle Event

## Plan vs capability

A **Capability** says what Bunova can enable/configure for an organization or branch.

A **Commercial Entitlement** says what the organization's current plan/license is allowed to enable or consume.

These concepts MUST remain separate. A plan can entitle `gaming` while the café chooses not to enable it. An organization cannot enable a capability it is not commercially entitled to unless a controlled trial/override grants it.

Plan changes are versioned so historical invoices and entitlements remain explainable.

## Commercial dimensions

Plans may constrain or price by explicit dimensions such as:

- number of branches;
- registered/active POS or operational devices;
- enabled premium capability families;
- advanced multi-branch/analytics features;
- support tier;
- selected paid integrations or compliance service;
- storage/export/API allowances where commercially justified.

Avoid per-seat/cashier friction unless real commercial evidence supports it. Operational safety must never depend on a confusing pricing limit.

## Subscription lifecycle

Recommended normalized states:

`trialing -> active -> past_due/grace -> suspended -> cancelled/expired`

Renewal/cancellation/reactivation transitions preserve period and payment history. Provider-specific subscription states remain behind an adapter.

## Offline and grace safety

A café must not lose an active shift or corrupt open sales because a device cannot contact Bunova Cloud at the exact instant a subscription changes.

License/entitlement projection to devices includes signed/versioned status and a deliberate grace policy. Expired/suspended organizations transition according to configured safety rules: block new privileged/configuration actions first, preserve data access/export and close/reconcile existing operational work where policy permits, and never silently destroy local data.

Offline grace is bounded and auditable; it is not an indefinite license bypass.

## Platform Super Admin

Platform operators need a separately authorized surface for:

- organizations/brands/branches overview;
- plan/subscription/trial state;
- entitlement/limit usage;
- platform payment/reconciliation exceptions;
- integration/service health;
- support cases and diagnostic bundles;
- tenant lifecycle/export/retention operations;
- platform-wide audit and abuse/security controls.

Platform operators MUST NOT casually browse café customer or transaction data. Access is least-privileged, purpose-bound and audited.

## Support access

Prefer explicit support grants and diagnostics over hidden impersonation. If impersonation/break-glass support is implemented, it requires strong privilege, reason/ticket reference, time-bound scope, conspicuous audit and preferably customer-visible history/notification where appropriate.

Never make direct production DB editing the normal support path.

## Tenant lifecycle

Organization lifecycle includes onboarding, active operation, plan change, grace, suspension/reactivation, export, closure and retention/deletion processes.

Suspension does not delete operational/fiscal records. Data export/deletion distinguishes legally retained financial/fiscal history from removable profile/content data.

## Platform billing provider

Use a provider-neutral subscription/payment adapter where online SaaS billing is enabled. Manual/admin-recorded commercial payment can exist for launch markets but must remain auditable and reconcilable.

Unknown provider payment state follows the same principle as operational payments: do not guess; reconcile.

## Public commercial surface

Before public launch, Bunova needs a credible marketing/pricing/signup/trial or sales-lead experience appropriate to the go-to-market strategy, with Arabic/English, responsive premium UX, transparent plan differences and current SEO/content requirements. AI-ExecutionKit SEO/Content authorities should govern this after K00 integration.

## Non-goals

- full general-ledger/accounting for Bunova the company;
- hidden arbitrary feature disabling inside an active shift;
- coupling operational café totals to SaaS subscription invoices;
- unlimited platform-support access to tenant data.
