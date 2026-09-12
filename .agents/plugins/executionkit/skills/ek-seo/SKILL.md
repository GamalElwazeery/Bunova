---
name: ek-seo
description: Inspect and validate the current project's SEO OS applicability, search-surface contract and candidate-bound release gate without creating task state.
---
Prefer `executionkit_seo_status` for read-only inspection and `executionkit_seo_validate` for explicit validation when the MCP surface is available. Otherwise run `node .executionkit/runtime/kit/bin/validate-seo.mjs .`; add `--release` only at the canonical candidate/release boundary. Treat `NOT_APPLICABLE` as a valid evidence-backed result, never as a shortcut for an uninspected public surface. Route remediation through canonical TODO and Agent OS SEO specialists. Do not equate Lighthouse success, route existence or repository structure with indexing, ranking, rich results or traffic.
