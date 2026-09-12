# Capability Configuration Contract

## Principle

Capabilities determine available behavior. Presets only select an initial capability set.

## Capability record

Each capability definition has stable key, version, dependencies, conflicts if any, required configuration validators, supported surfaces and optional migration hooks.

## Enable contract

Enabling requires:

- all dependencies enabled or enabled atomically;
- required configuration supplied or capability enters explicit `needs_configuration` state rather than pretending ready;
- permissions/routes/navigation exposed only after capability availability is valid;
- affected local devices receive configuration sync.

## Disable contract

Disabling blocks new activity but MUST preserve historical data. It must reject/require resolution when live sessions/resources/orders depend on the capability.

## Preset contract

A preset is a named versioned list of capabilities/default configuration suggestions. Applying a preset after onboarding is a reviewed configuration change, not destructive reset.

## Testing

Every capability dependency edge, disable-with-active-data case, branch override rule and offline stale-capability case requires automated coverage.
