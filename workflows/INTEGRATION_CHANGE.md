# Workflow: External/Inter-product Integration Change

1. Identify ownership and contract version.
2. Read adapter/integration contract.
3. Update schema/contract first for intentional breaking/additive behavior.
4. Implement provider/client adapter behind boundary.
5. Add fake/fixture contract tests.
6. Test duplicate, timeout, outage, malformed/auth failure, retry and reconciliation.
7. Add observability/support setup docs.
8. Verify no provider-specific leakage into unrelated core models.
9. Coordinate compatibility/migration before removing old version.
