# Onboarding and Setup Experience

## Goal

A small café should reach a valid first sale quickly without weakening configuration correctness for larger venues.

## Setup wizard

1. Organization / brand identity.
2. Country, timezone, currency, language and fiscal mode.
3. First branch and business day/opening hours.
4. Select café archetype preset.
5. Review/adjust enabled capabilities.
6. Create/import menu/catalog.
7. Configure taxes/pricing basics.
8. Configure venue/resources if enabled.
9. Configure production stations.
10. Register POS/operational device and printer.
11. Add staff/permissions.
12. Configure payment methods.
13. Optional integrations: Menuza, MikroTik, fiscal, external payment.
14. Validate readiness checklist and run test sale.

## Preset principle

Preset chooses defaults only. UI explains capabilities and allows safe adjustment. Enabling a capability runs dependency checks; disabling preserves history and warns about active resources/sessions/configuration.

## Catalog import

Support CSV/spreadsheet import first; image/PDF/AI-assisted menu extraction can be added as an assistive workflow with mandatory human review before publication. Imports preview changes and are reversible before commit.

## Hardware test

Wizard can test receipt printing, drawer where supported, customer display, station printing/KDS, router health and payment-provider connection without creating real revenue.

## Go-live readiness

Do not show generic "setup 100%" based on clicks. Readiness validates actual required configuration for enabled capabilities and lists blockers/warnings.
