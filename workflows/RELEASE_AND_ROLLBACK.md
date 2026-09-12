# Workflow: Release and Rollback

1. Verify launch gate and no open blocker findings.
2. Verify version compatibility across backend, web, Flutter apps, sync schema and integrations.
3. Run production migration dry-run and backup/restore evidence.
4. Verify secrets/config/integration endpoints and fiscal/payment environments.
5. Run smoke on critical journeys in staging/pilot configuration.
6. Deploy progressively with observability dashboards/alerts active.
7. Monitor money/fiscal/sync/queue errors and business health.
8. If rollback is required, follow migration compatibility plan; never restore database blindly over accepted production transactions.
9. Record release evidence and incidents as canonical follow-up tasks where needed.
