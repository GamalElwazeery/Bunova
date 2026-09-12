# Gaming and Timed Services

## Relationship to Timed Resource Engine

Gaming is a domain extension over the generic Timed Resource Engine. It adds gaming semantics; timing/rating/billing remain canonical shared behavior.

## Gaming entities

- gaming device/resource (PS4/PS5/Xbox/PC/VR/etc.);
- room/zone;
- controller/accessory availability metadata;
- supported player capacity;
- gaming title/catalog metadata optional;
- gaming rate plan;
- package/member entitlement;
- gaming reservation;
- gaming session linked to timed session;
- maintenance/out-of-service record.

## Session workflow

Select available device/room -> select rate/player/package -> start -> optional food/drink orders -> pause/resume if policy allows -> transfer if needed -> end -> rate -> attach charge to open venue bill or settle directly.

## Pricing dimensions

Resource tier, number of players/controllers, daypart, weekday/weekend, VIP room, minimum duration, package credits, membership discount and promotional bundle.

Applied pricing must be frozen/snapshotted for dispute resolution.

## Packages

Examples: 2-hour PS5 package, 10-hour prepaid wallet, gamer membership with monthly included hours, PS5 + drinks bundle. Entitlement consumption uses a ledger; no mutable "hours left" without movement history.

## Reservations

Gaming reservation locks a compatible resource/time window according to conflict policy and may require deposit. Arrival can start the session; late/no-show rules are configurable.

## Device/asset health

Operational status supports maintenance and controller/accessory issues. Future IoT power control is an adapter; billing truth never depends solely on smart-plug state.

## Abuse controls

Historical duration/rate adjustments require elevated permission, reason and audit. Session ownership collisions, forgotten open sessions and device clock drift must be detected.
