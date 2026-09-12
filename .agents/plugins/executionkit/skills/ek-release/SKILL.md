---
name: ek-release
description: Validate exact candidate/evidence, applicable SEO S3 and ExecutionKit release readiness.
---
Build/validate the exact candidate, collect configured evidence, run project-native release gates and `node .executionkit/runtime/kit/bin/release-readiness.mjs .`. When SEO is configured/applicable, the release path must include the candidate-bound SEO S3 contract/hash validation; use `node .executionkit/runtime/kit/bin/validate-seo.mjs . --release` for direct diagnosis, not as a substitute for Launch OS. Never convert missing adapters, search-engine observations or evidence into PASS.
