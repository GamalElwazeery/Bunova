# Platform Entitlement and Subscription Contract

## Separation invariant

Platform subscription/licensing is a separate financial/bounded context from café customer billing. No Platform Invoice/Payment row may be used as a café Bill/Payment or vice versa.

## Entitlement evaluation

Effective permission to enable/use a commercial feature is derived from:

1. organization subscription/trial state;
2. versioned plan entitlements and limits;
3. explicit time-bound commercial override/trial grant, if any;
4. organization/branch capability configuration;
5. normal staff/device authorization.

Passing one layer never bypasses another.

## Entitlement projection

Operational devices receive a signed/versioned entitlement projection sufficient for offline-safe decisions. Projection includes issued/expiry/grace metadata and must be refreshable/revocable. Clients do not invent entitlements from hidden UI state.

## Limit semantics

Branch/device/capability limits must have deterministic measurement, clear operator feedback and race-safe enforcement. Crossing a limit never deletes existing data. Limit reduction with existing usage above target requires an explicit remediation/grandfathering path.

## Subscription transitions

Trial start/end, activation, renewal, plan change, grace, suspension, cancellation and reactivation are append/audit-oriented lifecycle events. Historical plan version and commercial period remain reproducible.

## Suspension safety

Suspension policy must distinguish:

- new configuration/provisioning;
- new sales/session starts;
- closure/settlement of existing operations;
- read/export/support access;
- fiscal/payment/sync jobs that must continue to reconcile accepted historical transactions.

Never strand accepted money or fiscal work solely because a SaaS renewal changed state.

## Provider idempotency

Online SaaS billing provider create/renew/cancel/payment callbacks are authenticated, idempotent and reconcile unknown/duplicate/out-of-order states.

## Support override

Commercial/support overrides require actor, reason/ticket, exact entitlement/scope, start/end, approval where configured and immutable audit. Permanent undocumented override is prohibited.
