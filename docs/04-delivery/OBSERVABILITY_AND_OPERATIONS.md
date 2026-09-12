# Observability and Production Operations

## Structured telemetry

Every request/job/integration/sync command should carry correlation/trace identifiers. Structured logs include organization/branch/device context where safe, but never leak secrets or unnecessary customer data.

## Key metrics

API latency/error, queue depth/age, Reverb connectivity, DB saturation, sync backlog, command rejection/conflict, payment unknown state, fiscal pending/rejected, Menuza publication lag, router health, printer/device heartbeat where available and notification provider failure.

## Business health monitors

Stale open shifts, stale venue/timed sessions, unusually old production tickets, unreconciled external payments, fiscal backlog, stock projection errors and branch devices with prolonged unsynced data.

## Error tracking

Unhandled exceptions include release/build, route/job, branch/device context and correlation IDs with PII redaction. Client apps retain bounded local diagnostic logs and can generate a support bundle.

## Runbooks

Before launch, write runbooks for: backend outage, database failover/restore, Redis/queue outage, Reverb outage, payment provider outage, ETA outage, Menuza outage, router/MikroTik outage, printer failure, device replacement, sync conflict storm, accidental catalog publication and compromised credential/device.

## SLO direction

Define measured launch SLOs for API availability, order acceptance, sync recovery and fiscal backlog once staging/load data exists. Do not invent unmeasured 99.99% claims in planning.
