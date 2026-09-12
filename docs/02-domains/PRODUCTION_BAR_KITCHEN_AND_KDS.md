# Production: Bar, Kitchen, Shisha and KDS

## Objective

Route each accepted order line to the correct preparation station and track preparation independently from payment/order state.

## Station model

Examples: Coffee Bar, Cold Bar, Kitchen, Bakery/Dessert, Shisha, Packaging/Pickup.

A branch configures stations, device/screens/printers, operating hours, preparation capability and fallback routing. Products/variants/modifiers can influence routing.

## Production ticket model

Order acceptance generates station-scoped production tickets/items. One customer order may fan out to several stations and later converge for handoff.

Each production item stores item snapshot, modifiers/notes, quantity, source order/table/pickup context, priority, promised time if applicable and state timestamps.

## State behavior

Queued -> Preparing -> Ready -> Served/Handed-off.

Configurable acceptance/hold states may exist. Cancellation after preparation requires reason and may create waste/remake facts. Remake must link to the original item, not masquerade as a new unrelated sale.

## Barista mode

Optimized for drink modifiers, large touch targets, timers, compact queue, batch/sequence awareness, and clear dine-in/takeaway/gaming location context.

## Kitchen interoperability

Before building advanced KDS behavior, inspect the Restaurant System for reusable domain patterns/contracts. Shared extraction is preferred when stable. Bunova can use a café-specific presentation but must not fork fundamental production semantics casually.

## Handoff

Ready state can trigger waiter/pickup/customer-display notification. Multi-station orders can show partial readiness; policy decides whether pickup is announced only when all required items are ready.

## Metrics

Queue length, queue age, prepare duration by item/station/daypart, remake/cancel count, ready-to-serve delay and throughput. Metrics are derived from durable state timestamps.
