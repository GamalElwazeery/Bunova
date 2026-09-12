# Native Planning Audit — Bunova

## Scope

Audit performed after creation of the canonical `TODO.md` and native product/architecture/domain/experience/delivery/governance/contracts/rules/skills/agents/workflows corpus, followed by a second pass treating Bunova as a sellable SaaS product and as a real café operating system rather than only an application architecture.

## Repository evidence

The live repository tree was inspected after TODO creation and planning corrections. The expected authority families exist: root `README.md`, `AGENTS.md`, `TODO.md`; product/architecture/domain/experience/delivery/governance docs; contracts; rules; skills; agents; workflows. Repository search returned no unresolved critical `TBD` token at the first closure audit. Subsequent findings were normalized into stable authorities and canonical TODO tasks rather than a shadow backlog.

## Findings and corrections

### F1 — K00 sequencing missing from stable Phase Plan
Severity: planning P1.

`TODO.md` correctly required a dedicated AI-ExecutionKit integration gate between native planning and product implementation, while `PHASE_PLAN.md` originally jumped directly from P00 to P01.

**Correction:** K00 added explicitly to Phase Plan; product implementation is blocked until both P00 and K00 close.

### F2 — Readiness wording could authorize P01 too early
Severity: planning P1.

The original readiness checklist said P00 closure could open P01 although the requested sequence is native planning first, AI-ExecutionKit integration next, product implementation after that.

**Correction:** readiness split into native P00 readiness and pre-implementation K00 readiness.

### F3 — Café-specific operational details needed explicit authority
Severity: planning P2.

Several commercially common requirements were implicit but not explicit enough: table/session minimum-spend or cover rules, pre-generated/batch printed Wi-Fi cards, basic worker wages/advances/deductions/overtime/payout evidence, opening/handover/closing checklists, operational supplier dues and controlled customer tabs/house accounts.

**Correction:** explicit venue minimum-spend rules, Wi-Fi batch-voucher authority, Payroll-Lite workforce-cost authority, operational checklists, supplier operational payable/settlement rules and customer house-account ledger rules were added. Full statutory HR/payroll/general-ledger accounting remains a non-goal unless separately expanded.

### F4 — Product architecture covered tenants but not Bunova Cloud commercialization deeply enough
Severity: planning P1.

The initial corpus modeled organizations/branches and capabilities but did not separately define the company-side SaaS layer: plan versions, trials/subscriptions, commercial entitlements/limits, platform billing, offline license grace, Platform Admin/support controls, tenant lifecycle and the public Bunova commercial surface.

This omission could have caused capability flags to become licensing rules, mixed café customer payments with SaaS charges, or created unsafe suspension behavior during an open shift.

**Correction:** added `PLATFORM_SAAS_AND_TENANT_LIFECYCLE.md`, `PLATFORM_ENTITLEMENT_AND_SUBSCRIPTION_CONTRACT.md`, the SaaS entitlement safety skill, Platform Operations agent, Platform Admin/public website surfaces, domain ownership separation and detailed P01/P17/P19/P20/P21 canonical tasks. Platform Billing and Café Billing are explicitly independent bounded contexts.

## Completeness result

The native planning corpus now covers:

- Bunova Cloud multi-tenancy, plan/trial/subscription commercial entitlements, platform administration/support, tenant lifecycle and public commercial onboarding;
- kiosk/cart/drinks-only/table-service/café+restaurant/traditional/gaming/internet/multi-branch compositions;
- counter, takeaway, dine-in/waiter, QR/online pickup/delivery-entry channels;
- bar/kitchen/KDS/shisha production;
- tables/rooms/service sessions and minimum-spend/cover;
- PlayStation/gaming and generic timed resources;
- MikroTik-first Wi-Fi packages, individual and batch voucher cards, quotas and captive-portal boundary;
- inventory/recipes/procurement/waste/COGS plus operational supplier dues/settlements;
- staff/attendance/permissions/tips/commissions/Payroll-Lite workforce cost and operational shift checklists;
- Menuza and Restaurant inter-product boundaries;
- loyalty/promotions/memberships/bundles/gift value and controlled customer house accounts;
- reservations, fiscal/expenses/day close;
- offline/sync including commercial-entitlement grace, devices/hardware, analytics, premium UX, Arabic/RTL/accessibility, security/testing/audit/launch.

No product code has been introduced by planning. AI-ExecutionKit remains intentionally absent pending K00.

## Result

**Native Bunova planning is closed: `P00-GATE` is complete. The only allowed next frontier is K00 AI-ExecutionKit deep integration; P01 product implementation remains blocked until K00 closes.**
