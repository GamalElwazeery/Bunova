# Hardware, Notifications and Operational Assets

## Peripheral classes

- thermal receipt printers (58/80mm where supported);
- kitchen/bar printers;
- cash drawers;
- barcode scanners;
- customer-facing displays;
- KDS/bar displays;
- payment terminals/SoftPOS adapters;
- routers/access points through Wi-Fi adapter;
- optional smart plugs/IoT later.

## Device registry

Each Bunova operational device/register has branch, device ID, type/mode, capabilities, app version, last seen, sync status, credential state and peripheral configuration. Device registration/revocation is controlled and auditable.

## Printing

Printing is template-based with branch/locale/fiscal requirements, retry visibility and duplicate/reprint marking. Kitchen/bar print failure must surface operationally; silent spool failure is unacceptable.

## Customer display

Can show cart totals, promotions, payment state, pickup token and post-sale loyalty/menu QR. It must not expose private customer data to the next customer after transaction close.

## Asset management

Operational assets such as espresso machine, grinder, blender, freezer, console, controller, TV, router and printer can carry serial/model/location, status, warranty and maintenance history. Asset lifecycle is separate from consumable stock.

## Notifications

Normalize event -> recipient/audience -> template -> channel -> provider attempt. Channels may include in-app/realtime, push, SMS, WhatsApp and email. Provider-specific adapters do not own order/reservation truth.

## Alerting

Operational alerts include router down, station/display offline, printer failure, sync backlog, fiscal rejection, stock threshold and stale/open-session anomalies. Alerts require severity, deduplication and acknowledgment semantics.
