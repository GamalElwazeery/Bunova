# Staff Operations Domain

## Scope

Bunova models operational staff required to run a café, without becoming a full payroll/HR system in the first frontier.

Typical roles: owner, branch manager, supervisor, cashier, waiter, barista, juice maker, chef/cook, kitchen helper, shisha worker, gaming supervisor, cleaner/service staff and delivery handoff staff.

## Identity and access

Staff profile is distinct from authentication credential. One person may have branch assignments and role/permission sets. Quick PIN/biometric device unlock may switch operational actor but server authorization remains canonical.

## Operational shift

Staff can be scheduled/assigned to a branch/shift/station/table zone. Clock-in/out and breaks are recorded when attendance capability is enabled. Cashier financial shift remains a separate cash-control aggregate linked to staff.

## Assignments

- waiter -> floor/zone/tables;
- barista/cook -> production station;
- gaming supervisor -> gaming zone;
- manager -> branch operational authority.

Assignments affect routing/UI defaults, not immutable authorization unless permission policy explicitly says so.

## Tips, commissions and staff consumption

Tip attribution can be pooled, staff-specific or disabled. Commission rules are optional and transparent. Staff meal/drink/complimentary consumption uses explicit reason/allowance rules and stock/billing treatment rather than invisible deletion.

## Performance signals

Operational metrics may include service response, orders handled, preparation time, remakes and sales influence. These are decision-support metrics and must be contextualized; avoid simplistic leaderboards that encourage unsafe behavior.

## Sensitive actions

Refund, void, discount override, price override, complimentary item, drawer action, shift variance approval, historical duration edit and stock adjustment all require granular permissions and audit.
