# Migration, Support and Launch

## Data migration/import

Provide validated imports for catalog, customers/loyalty where lawful, opening stock, suppliers and possibly gift/prepaid balances with explicit reconciliation. Financial history migration is a separate high-risk project and should not be promised by generic CSV import.

Imports use preview -> validation -> commit -> result report. Idempotent rerun/rollback-before-go-live is planned where feasible.

## Pilot rollout

Launch path: internal demo branch -> controlled pilot café -> monitored real shifts -> multi-device/multi-capability pilot -> broader release. Pilot success criteria include offline event, shift reconciliation, production/timed-resource flow, fiscal behavior and support load.

## Backup/restore

Automated PostgreSQL/object-storage backup policy, restore verification, retention and disaster drill are launch gates. A backup that has never been restored is not accepted evidence.

## Release/rollback

Version backend/API/mobile schema contracts; database migrations are forward-safe with documented rollback/roll-forward strategy. Mobile/POS must tolerate supported backend version skew during staged rollout.

## Support

Owner/admin help, contextual setup guidance, device diagnostics, support bundle, known-issue communication and escalation/runbook ownership. Support operators receive least-privileged tools; no direct production DB editing as routine support.

## Launch blockers

Open P0/P1 audit findings, unresolved money/fiscal reconciliation defects, tenant isolation failure, untested backup restore, missing legal/fiscal configuration, unsupported critical device flow, production demo data, or unknown payment success behavior block launch.
