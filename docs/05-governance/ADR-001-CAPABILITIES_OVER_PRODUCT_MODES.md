# ADR-001: Capabilities over Product Modes

**Status:** Accepted

## Decision
Model Bunova as one product with independently enabled capabilities. Coffee Kiosk, Gaming Café, Café & Restaurant and similar labels are versioned onboarding presets, not separate runtime architectures.

## Why
Cafés mix features unpredictably. Product modes would create forks, duplicate workflows and migration pain. Capabilities permit a kiosk to remain simple while a venue enables table/kitchen/gaming/Wi-Fi without changing core identity.

## Consequences
Capability dependencies/validation are first-class. Navigation/configuration adapts dynamically. Historical data survives disable. Tests cover combinations and dependency constraints.
