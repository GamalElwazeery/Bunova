# Localization, Accessibility and Input

## Languages

Arabic is a first-class language, not a translated afterthought. English is first-class. Domain labels, errors, receipts, fiscal/customer output and Menuza integration fields are localizable where appropriate.

## RTL

All layouts, icons with directional meaning, drawers, breadcrumbs, tables, charts, floor-map controls and form groups are reviewed in RTL. Numeric/money/phone/code tokens use explicit bidi handling where required.

## Date/time/number

Display uses branch locale/timezone policy. Store timestamps canonically. Monetary formatting uses currency and business policy; never parse formatted display strings for calculations.

## Accessibility

Web targets practical WCAG 2.2 AA behavior for administrative/customer-accessible areas. Flutter operational surfaces require semantic labels, focus order, sufficient contrast, scalable text where workflow permits and alternatives to color-only status.

## Touch targets

Operational touch controls target at least ~44–48 logical pixels, larger for production/gaming grids viewed at distance. Destructive controls are spatially separated from common actions.

## Keyboard/scanner input

Desktop POS supports keyboard shortcuts for high-frequency actions and barcode-scanner keyboard wedge behavior where configured. Focus must remain deterministic after scans and payment dialogs.

## Sound

Production/service alerts offer audible feedback with volume/mute policy; sound is never the sole indicator.
