# Staff Operations Domain

## Scope

Bunova models operational staff required to run a café without becoming a full HR/compliance payroll ERP in the first frontier.

Typical roles: owner, branch manager, supervisor, cashier, waiter, barista, juice maker, chef/cook, kitchen helper, shisha worker, gaming supervisor, cleaner/service staff and delivery handoff staff.

## Identity and access

Staff profile is distinct from authentication credential. One person may have branch assignments and role/permission sets. Quick PIN/biometric device unlock may switch operational actor but server authorization remains canonical.

## Operational shift

Staff can be scheduled/assigned to a branch/shift/station/table zone. Clock-in/out and breaks are recorded when attendance capability is enabled. Cashier financial shift remains a separate cash-control aggregate linked to staff.

## Opening, handover and closing checklists

A café may configure operational checklist templates by branch, role, station or shift/daypart. Examples include espresso-machine warmup/cleaning, grinder calibration, fridge/stock check, printer/router/PS device check, floor/table readiness, cash handover, kitchen/bar sanitation, closing cleaning and unresolved-session review.

Checklist items may require simple completion, note, quantity/reading, photo/attachment where justified, or manager verification. Completion records actor/time/shift/station and unresolved exceptions. Checklist evidence supports operations and accountability but never substitutes for financial/stock/system state that already has a canonical domain.

## Assignments

- waiter -> floor/zone/tables;
- barista/cook -> production station;
- gaming supervisor -> gaming zone;
- manager -> branch operational authority.

Assignments affect routing/UI defaults, not immutable authorization unless permission policy explicitly says so.

## Tips, commissions and staff consumption

Tip attribution can be pooled, staff-specific or disabled. Commission rules are optional and transparent. Staff meal/drink/complimentary consumption uses explicit reason/allowance rules and stock/billing treatment rather than invisible deletion.

## Workforce cost / Payroll-Lite

When enabled, Bunova may track operational workforce cost without pretending to be a statutory payroll/HR suite:

- wage basis: monthly, daily, hourly or shift-based;
- expected work/attendance link where configured;
- overtime/extra-shift quantity and approved rate;
- advances/loans paid to staff;
- explicit deductions/bonuses with reason and approval;
- payout records and period summary;
- staff-cost analytics by branch/period.

Every financial movement is auditable. Bunova must clearly label calculated Payroll-Lite summaries and avoid claiming tax/social-insurance/legal payroll compliance unless a future jurisdiction-specific module explicitly implements it.

## Performance signals

Operational metrics may include service response, orders handled, preparation time, remakes and sales influence. These are decision-support metrics and must be contextualized; avoid simplistic leaderboards that encourage unsafe behavior.

## Sensitive actions

Refund, void, discount override, price override, complimentary item, drawer action, shift variance approval, historical duration edit, stock adjustment, checklist verification override, wage change, advance, deduction and payout correction all require granular permissions and audit.
