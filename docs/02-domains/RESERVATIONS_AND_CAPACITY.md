# Reservations and Capacity

## Reservable targets

Tables/areas, rooms, gaming devices/resource classes and future timed resources may be reservable. A reservation requests a time range, capacity and compatible resource criteria; assignment may be fixed or deferred until arrival.

## Lifecycle

`tentative(optional) -> confirmed -> arrived -> seated/started -> completed`

Exception states: `cancelled`, `no_show`, `expired`.

## Conflict rules

Prevent overlapping confirmed allocation for exclusive resources while respecting setup/cleanup buffers. Resource-class reservations require deterministic assignment before or at check-in.

## Deposits

Reservation may require deposit/payment reference. Deposit application to final bill/refund/no-show treatment is explicit and auditable.

## Arrival conversion

Table reservation opens a venue session. Gaming/resource reservation starts/links a timed session. Historical reservation remains linked rather than being overwritten.

## Customer communication

Confirmations/reminders/cancellations are notification jobs; failed message delivery does not change reservation state.

## Offline policy

Creating/rescheduling reservations generally requires server authority to avoid conflict. Offline devices may read a cached near-term schedule and check in already-synced reservations under controlled rules.
