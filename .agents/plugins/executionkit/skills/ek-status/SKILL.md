---
name: ek-status
description: Inspect ExecutionKit lifecycle, runtime/State status, closure and configured SEO applicability without changing task state.
---
Use the `executionkit_status` MCP tool when available; otherwise run `node .executionkit/runtime/kit/bin/runtime-status.mjs .`. When SEO is configured, also inspect `executionkit_seo_status` (or `node .executionkit/runtime/kit/bin/validate-seo.mjs .`) so `APPLICABLE`, `NOT_APPLICABLE`, invalid-contract and release-policy states are visible. Report canonical state and blockers without changing task state; SEO/State observations never become a shadow backlog.
