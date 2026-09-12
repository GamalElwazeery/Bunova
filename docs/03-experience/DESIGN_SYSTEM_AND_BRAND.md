# Bunova Design System and Brand Direction

## Brand personality

Bunova should feel modern, warm, operational and premium without becoming a cliché coffee-themed UI. Avoid visual dependence on coffee-bean illustrations, fake wood textures or ornamental café motifs.

## Proposed base palette direction

Use semantic tokens rather than raw colors in components. Initial brand direction:

- **Espresso Ink** — near-black warm neutral for dark surfaces/text.
- **Warm Paper** — soft warm light background.
- **Roast / Copper accent** — energetic warm accent for brand/action emphasis.
- semantic Success / Warning / Danger / Info palettes with accessible contrast.

Exact production color values require visual contrast testing across light/dark themes before implementation freeze.

## Typography

Arabic: GE SS Two family where licensed/provided, with high-quality system Arabic fallback.

English/numbers: Poppins where licensed/packaged appropriately, with system sans fallback.

Numerical POS values require excellent tabular legibility; use tabular-number support where the selected font permits.

## Component system

Core tokens: color, typography, spacing, radius, elevation, motion, opacity, breakpoints, touch target and state layers.

Required components include app shells, navigation, command bar, cards, metric tiles, data grid/table, filter/sort, segmented control, tabs, chips/status, product tile, cart line, modifier selector, numeric keypad, money input, timer/session card, floor resource tile, production ticket, dialog/drawer/sheet, toast/banner, offline/sync indicator, empty/error state, skeleton and chart primitives.

Flutter and web components must map to the same semantic tokens even if implementation differs.

## Theme

Light and dark themes are supported where operationally appropriate. Dark theme must remain legible in dim venues; light theme must resist glare. Do not use translucent/glass effects that sacrifice readability.

## Motion

Short functional motion only: state transition, item insertion/removal, navigation feedback. Honor reduced-motion preference; do not animate timers/queues in a distracting way.
