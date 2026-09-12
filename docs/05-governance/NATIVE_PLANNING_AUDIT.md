# Native Planning Audit — Bunova

## Scope

Audit performed after creation of the canonical `TODO.md` and native product/architecture/domain/experience/delivery/governance/contracts/rules/skills/agents/workflows corpus.

## Repository evidence

The live repository tree was inspected from the post-TODO `main` commit. The expected authority families exist: root `README.md`, `AGENTS.md`, `TODO.md`; product/architecture/domain/experience/delivery/governance docs; contracts; rules; skills; agents; workflows. Repository search returned no unresolved `TBD` token at audit time.

## Findings and corrections

### F1 — K00 sequencing missing from stable Phase Plan
Severity: planning P1.

`TODO.md` correctly required a dedicated AI-ExecutionKit integration gate between native planning and product implementation, while `PHASE_PLAN.md` originally jumped directly from P00 to P01.

**Correction:** K00 added explicitly to Phase Plan; product implementation is blocked until both P00 and K00 close.

### F2 — Readiness wording could authorize P01 too early
Severity: planning P1.

The original readiness checklist said P00 closure could open P01 although the user explicitly scheduled kit integration next.

**Correction:** readiness split into native P00 readiness and pre-implementation K00 readiness.

### F3 — Café-specific operational details needed explicit authority
Severity: planning P2.

Three commercially common requirements were implicit but not explicit enough: table/session minimum-spend or cover rules, pre-generated/batch printed Wi-Fi cards, and basic worker wages/advances/deductions/overtime/payout evidence.

**Correction:** explicit venue minimum-spend rules, Wi-Fi batch-voucher authority and Payroll-Lite workforce-cost authority were added. Full statutory HR/payroll remains a non-goal unless separately expanded.

## Completeness result

The native planning corpus now covers:

- kiosk/cart/drinks-only/table-service/café+restaurant/traditional/gaming/internet/multi-branch compositions;
- counter, takeaway, dine-in/waiter, QR/online pickup/delivery-entry channels;
- bar/kitchen/KDS/shisha production;
- tables/rooms/service sessions and minimum-spend/cover;
- PlayStation/gaming and generic timed resources;
- MikroTik-first Wi-Fi packages, individual and batch voucher cards, quotas and captive-portal boundary;
- inventory/recipes/procurement/waste/COGS;
- staff/attendance/permissions/tips/commissions/payroll-lite workforce cost;
- Menuza and Restaurant inter-product boundaries;
- loyalty/membership/bundles/stored value, reservations, fiscal/expenses/day close;
- offline/sync, devices/hardware, analytics, premium UX, Arabic/RTL/accessibility, security/testing/audit/launch.

No product code has been introduced by planning. AI-ExecutionKit remains intentionally absent pending K00.

## Result

**Native planning: ready to close P00 after the canonical TODO is updated to record these audit corrections and gate completion.**
