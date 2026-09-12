# Order, Venue Session and Production Lifecycles

## Order channels

`counter`, `takeaway`, `dine_in`, `waiter`, `qr_table`, `online_pickup`, `online_delivery`, `phone`, `gaming_session`, and future versioned channels.

Channel is context, not a forked order implementation.

## Order lifecycle

Recommended canonical business states:

`draft -> submitted -> accepted -> in_fulfillment -> ready/partially_ready -> completed`

Exception states include `rejected`, `cancelled`, and item-level cancellation/remake. Payment status and production status are separate dimensions; do not overload order state with settlement.

## Venue service session

A table/room visit has a session independent of any one order:

`opened -> active -> bill_requested(optional) -> settling -> closed`

The session may contain multiple orders over hours, timed-resource links, shisha/service calls and one or multiple split bills.

Transfers/merges create explicit history. Closing requires configurable checks for open orders, unbilled timed usage and unsettled bills.

## Production ticket/item lifecycle

Production is item/station scoped:

`queued -> accepted(optional) -> preparing -> ready -> handed_off/served`

Exception transitions: `held`, `cancelled`, `remake_requested`, `remade` or station-specific equivalents defined canonically.

Order completion is derived from required fulfillment and settlement policy, not from one kitchen ticket state.

## Takeaway identity

Human pickup sequence (for example A41) is branch/day scoped and separate from globally unique order IDs. Sequence rollover and duplicate prevention must be deterministic even with offline registers.

## State transition contract

Every transition has:

- allowed source states;
- required permission/actor;
- validation preconditions;
- durable event/audit where sensitive;
- idempotency behavior;
- side effects defined outside the transition transaction when external.

No UI client is allowed to set arbitrary state strings.
