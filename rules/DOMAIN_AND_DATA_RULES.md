# Domain and Data Rules

- Capability presets never fork domain implementations.
- One canonical billing engine handles product/time/access/service value.
- Gaming extends Timed Resources; it cannot own a second timer/rating/payment engine.
- Menuza does not own Bunova operational settlement/stock/fiscal truth.
- Analytics/read models never become write authorities.
- Finalized financial, stock-ledger, stored-value, timed-usage and fiscal history uses compensating/versioned changes, not silent mutation.
- Every operational aggregate has explicit organization/branch ownership.
- Historical records snapshot changing master data required for explanation.
- Cross-domain side effects use explicit commands/events/contracts, not hidden model hooks with unclear ownership.
