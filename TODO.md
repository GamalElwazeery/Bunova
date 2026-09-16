# Bunova Canonical TODO

> **Sole mutable execution/backlog authority.** Do not create shadow TODOs, audit backlogs, phase checklists, issue queues, or private execution lists. Stable plans/contracts/rules describe truth; this file records execution state.

## Status semantics

- `[ ]` not started / no accepted implementation evidence.
- `[~]` implementation exists or started, but acceptance/DoD is incomplete.
- `[!]` objectively blocked; blocker/evidence must be written on the task.
- `[x]` complete under `docs/05-governance/STATUS_AND_DEFINITION_OF_DONE.md`.

## Global execution rule

Read `AGENTS.md`, inspect live repository state, select the earliest dependency-ready eligible task, read its linked authorities, inspect existing implementation, execute task-by-task, update this file after every completed/blocked task, and close a phase only through its gate. Heavy/full CI and independent audit run at phase/wave gates unless a task explicitly requires earlier focused verification.

---

# P00 — Native planning closure

**Authorities:** `README.md`, `docs/00-product/*`, `docs/01-architecture/*`, `docs/02-domains/*`, `docs/03-experience/*`, `docs/04-delivery/*`, `docs/05-governance/*`, `contracts/*`, `rules/*`, `skills/*`, `agents/*`, `workflows/*`.

- [x] `P00-001` Establish repository as planning-first Bunova Café Management OS and state implementation boundary. **Accept:** README identifies scope, boundaries, technology baseline, canonical entry points and defers AI-ExecutionKit integration to dedicated round.
- [x] `P00-002` Freeze product vision, target café archetypes and product/non-product boundaries. **Accept:** kiosk, cart, coffee shop, traditional café, café+restaurant, gaming/internet café and multi-branch use cases are covered without separate architectures.
- [x] `P00-003` Define capability architecture and onboarding presets. **Accept:** capabilities/dependencies are architectural; presets are configuration only; disabling preserves history.
- [x] `P00-004` Define canonical glossary. **Accept:** organization/branch/order/bill/session/resource/station/voucher terms are unambiguous and overloaded session terminology is qualified.
- [x] `P00-005` Define bounded contexts and data ownership. **Accept:** every major domain has a canonical owner and forbidden ownership leakage is documented.
- [x] `P00-006` Accept unified billing architecture. **Accept:** products, services, timed usage and access converge through canonical billable lines with historical snapshots and refund rules.
- [x] `P00-007` Accept timed-resource architecture. **Accept:** usage segments, rating snapshots, pause/resume/transfer and offline safety are specified; Gaming extends rather than forks it.
- [x] `P00-008` Define order, venue-session and production lifecycles. **Accept:** states are separate dimensions with transition/permission/idempotency expectations.
- [x] `P00-009` Define offline synchronization architecture and contract. **Accept:** local projection, atomic outbox, idempotent ingestion, conflict classes, device revocation and convergence tests are specified.
- [x] `P00-010` Define Menuza ownership/integration boundary. **Accept:** catalog publication, QR context, order ingestion, status, payment ambiguity and reconciliation are covered without public-menu duplication.
- [x] `P00-011` Define Restaurant System reuse boundary. **Accept:** shared package/API/spec decision order and no-copy/paste rule are explicit.
- [x] `P00-012` Define payment, ETA fiscal and Wi-Fi router adapter contracts. **Accept:** provider leakage, idempotency, unknown states, retry and reconciliation are covered.
- [x] `P00-013` Define domain specs for catalog, POS, venue, production, inventory, staff, gaming, Wi-Fi, shisha, customers, reservations, fiscal, analytics and hardware.
- [x] `P00-014` Define product surfaces and premium UX standard. **Accept:** admin/owner, POS, waiter, production, gaming, customer display, Menuza/captive portal and diagnostics surfaces plus complete states are covered.
- [x] `P00-015` Define brand/design-system/localization/accessibility direction. **Accept:** semantic tokens/components, light/dark direction, Arabic/English/RTL, touch/keyboard and accessibility are planned.
- [x] `P00-016` Define test, audit, security, observability, migration/support and launch strategies. **Accept:** risk-driven evidence and launch blockers exist.
- [x] `P00-017` Add native Bunova rules, skills, agents and workflows. **Accept:** they guide work without becoming a shadow backlog.
- [x] `P00-018` Record accepted ADRs for capability architecture, unified billing, modular-monolith first, offline-first clients, Menuza layer and Restaurant reuse.
- [x] `P00-019` Create implementation readiness checklist and traceability/status/DoD authorities.
- [x] `P00-020` Perform final planning cross-reference audit after canonical TODO creation. **Depends:** P00-001..019. **Evidence:** `docs/05-governance/NATIVE_PLANNING_AUDIT.md`; repository tree checked, K00 sequencing/readiness mismatch and café-specific explicitness gaps repaired.
- [x] `P00-021` Run planning completeness audit against `IMPLEMENTATION_READINESS_CHECKLIST.md`. **Depends:** P00-020. **Evidence:** no unresolved critical `TBD`; future implementation domains have canonical phase/task coverage and all identified planning P1 gaps were corrected.
- [x] `P00-022` Extend planning closure to the sellable Bunova Cloud layer and remaining real-café financial operations. **Evidence:** `PLATFORM_SAAS_AND_TENANT_LIFECYCLE.md`, platform entitlement contract/skill/agent, Platform Admin/public surfaces, supplier dues, customer house accounts and operational shift checklists are now stable authorities with canonical phase tasks below.
- [x] `P00-GATE` Close native planning gate. **Depends:** P00-020..022. **Evidence:** `docs/05-governance/NATIVE_PLANNING_AUDIT.md` clean after corrections. **Next:** K00 only; product implementation remains blocked until K00 closes.

---

# K00 — AI-ExecutionKit deep integration gate (dedicated next round)

**Boundary:** Do not execute these tasks in the native-planning round. Live `Elwazeery/AI-ExcutionKit` repository truth and its current installer/version always win over assumptions below.

- [x] `K00-001` Inspect live AI-ExecutionKit `main`: HEAD, release/version, README/playbook, installer, system authorities, AGENTS/TODO contracts, MCP/tools/scripts and integration guidance. **Accept:** Bunova integration plan is based on current live kit, not remembered version. **Evidence:** Live kit pinned at commit `5a9eda4ab2159f93cca9867b492fe9ceb65ce261`, VERSION `3.0.0`; all 346 runtime tests pass on host; integration authorities in `docs/executionkit/ADOPTION.md` and `docs/executionkit/INTEGRATION_PLAN.md` reconciled.
- [x] `K00-002` Inspect Bunova `main` immediately before install and protect all native planning authorities from overwrite/regression. **Accept:** baseline HEAD/files recorded and any newer Bunova work preserved. **Evidence:** Pre-adoption baseline `ac61bdd65ef476d8961ab3534b84d46eed0a7369` documented in `docs/executionkit/PROJECT_STUDY.md`; all 11 native planning directories and contracts verified intact.
- [x] `K00-003` Run/use the kit's supported installer path for a planning-only repository. **Accept:** installer output is reviewable, deterministic and no product code is invented. **Evidence:** Verified via `bin/adopt-project.mjs --target . --json`; classified as mature planning repository; pinned isolated runtime at `.executionkit/runtime/kit`; TODO preserved; zero product code invented.
- [x] `K00-004` Normalize kit `AGENTS.md`/agent instructions with Bunova `AGENTS.md`. **Accept:** Bunova read order, live-repo rule, `TODO.md` single authority and domain-specific constraints remain explicit. **Evidence:** Verified `AGENTS.md` startup order, live-repo primacy, `TODO.md` single authority, `.agents/manifest.json` binding, and 8 café domain invariants; verified by `validate-execution.mjs`.
- [x] `K00-005` Integrate every system exposed by the live kit release, including cross-cutting state/continuity facilities where present. **Accept:** no installed system is merely copied; each is registered, routable and linked to Bunova workflows. **Evidence:** Execution, Agent, Content, SEO, Audit, Test, Launch, and State OS authorities integrated under `docs/` and verified; `validate-execution.mjs`, `validate-agents.mjs`, `validate-content-depth.mjs`, `validate-seo.mjs`, `validate-state.mjs`, and `validate-quality.mjs` all PASS.
- [x] `K00-006` Map Execution system to Bunova task/status/dependency semantics. **Accept:** `[ ]/[~]/[!]/[x]`, task-by-task selection, phase gates and no-shadow-backlog rules are preserved. **Evidence:** Verified via `node scripts/executionkit/project-tasks.mjs` and `bin/validate-execution.mjs`; all 457 tasks inspected with 0 errors; sequential phase gate dependencies and native marker semantics strictly preserved.
- [x] `K00-007` Map Agent system to Bunova native agent roles/skills/contracts. **Accept:** native specialists are retained and routable; no conflicting duplicate agent authority. **Evidence:** Verified via `bin/validate-agents.mjs` and `bin/agent-doctor.mjs`; all 10 native Bunova specialist roles, Antigravity host adapters, and routing policies in `.agents/manifest.json` PASS with zero errors.
- [x] `K00-008` Map Content system to Bunova Arabic/English UI/help/receipt/error/empty-state content and no-placeholder rules. **Evidence:** Verified via `docs/content/CONTENT_OS.md` and `bin/validate-content-depth.mjs`; Content OS configured FULL; zero placeholder text detected; bilingual Arabic/English operational and fiscal rules established.
- [x] `K00-009` Map SEO/public-discovery capabilities from live kit to Bunova public website/commercial acquisition surfaces only where appropriate; do not pollute operational POS/admin concerns. **Evidence:** Verified via `executionkit.seo.json` and `bin/validate-seo.mjs`; SEO OS STANDARD bounded strictly to public commercial acquisition and support pages; POS/admin/authenticated operational surfaces confirmed private and non-indexable.
- [x] `K00-010` Map Audit system to `AUDIT_STRATEGY.md`, phase close and anti-generic UX/domain integrity checks. **Evidence:** Verified via `docs/audit/AUDIT_OS.md` and `.agents/manifest.json`; independent Audit OS FULL configured; phase-boundary audits bound to `role.qa-auditor`; anti-generic UX, financial/offline invariant, and no-self-approval checks enforced.
- [x] `K00-011` Map Test system to Bunova risk-driven test strategy and phase/wave heavy-suite cadence. **Evidence:** Verified via `docs/testing/TEST_OS.md`, `execution.config.json`, and `bin/validate-ci-policy.mjs`; Test OS configured FULL; phase-boundary full CI cadence and targeted task-level verification policy enforced; self-hosted manual CI policy verified.
- [x] `K00-012` Map Launch system to Bunova migration/support/observability/backup/pilot/rollback gates. **Evidence:** Verified via `docs/launch/LAUNCH_OS.md` and `execution.config.json`; Launch OS configured FULL; exact-candidate release binding, restore drill, pilot rollout, and physical hardware/ETA evidence boundaries established.
- [x] `K00-013` Map live kit state/continuity system to resumable Bunova sessions without creating alternate backlog/state authority. **Accept:** checkpoint/rehydrate/amnesia cycle proven on live host; State OS never owns task state. **Evidence:** Verified on host via `bin/state-checkpoint.mjs`, `bin/state-rehydrate.mjs`, `bin/state-amnesia-test.mjs`, and `bin/validate-state.mjs`; zero drift detected; memory remains advisory.
- [x] `K00-014` Register/rout Bunova native skills, agents, workflows, rules and contracts through kit mechanisms, including Platform SaaS authorities. **Accept:** `registered == integrated` is not assumed; routing/selection is verified. **Evidence:** Verified via `resolveAgentContext` dry-runs; `K00-019` routes to `role.qa-auditor`, `P01-004` to `role.backend-engineer`, `P01-016` to `role.flutter-operations`, `P01-022` to `role.platform-operations`, `P05-001` to `role.integration-engineer`; 100% routable and zero unroutable resources.
- [x] `K00-015` Install/configure kit MCP/tools/scripts supported for Bunova repository and verify safe operation. **Evidence:** Verified via `bin/setup-tooling.mjs . --apply`; isolated tooling installed at `.executionkit/tooling`; `@modelcontextprotocol/server` and `zod` verified READY; MCP Server instance successfully verified.
- [x] `K00-016` Reconcile kit-generated TODO metadata with canonical Bunova `TODO.md` without shortening task detail or creating another TODO. **Evidence:** Verified via `scripts/executionkit/project-tasks.mjs` and `bin/validate-execution.mjs`; all 457 tasks hash-bound to `TODO.md` and `TODO_ARCHIVE.md`; single mutable backlog authority preserved; zero shadow queues created.
- [x] `K00-017` Run kit validators/readiness/audit commands applicable to a planning-first repo and fix every Bunova-specific conflict. **Evidence:** Verified via full validator suite execution; `doctor.mjs`, `validate-execution.mjs`, `validate-agents.mjs`, `validate-quality.mjs`, `validate-seo.mjs`, `validate-state.mjs`, `validate-ci-policy.mjs`, `validate-content-depth.mjs`, and `validate-v30-project.mjs` all exited with code 0.
- [x] `K00-018` Update Bunova README/playbook references for exact installed kit usage, start/resume prompt and execution commands. **Evidence:** Verified `README.md` and `scripts/executionkit/README.md`; start/resume prompt, exact pinned runtime commands, validator entries, MCP server path, and activation procedures documented and aligned.
- [x] `K00-019` Independently audit deep integration: all systems active/routable, native authorities preserved, no duplicate backlog, no broken paths, no accidental implementation. **Evidence:** Independent phase audit executed by `role.qa-auditor`; all 10 invariants verified (repository truth, 8 systems + Premium Experience, native planning preservation, sole backlog parity, runtime identity, 9/9 validator clean pass, State OS cycle, MCP tooling resolution, zero premature product code); formal report recorded in `docs/executionkit/K00_DEEP_INTEGRATION_AUDIT.md` with verdict PASS.
- [x] `K00-GATE` Close AI-ExecutionKit integration gate. **Depends:** K00-001..019. **Next:** P01 engineering foundation may start. **Evidence:** Project activated via `bin/activate-project.mjs . --apply`; `execution.config.json` set to `projectState: ACTIVE`; deep integration gate accepted and execution structural integrity verified with 0 errors; Phase K00 closed, unblocking Phase P01.

---

# P01 — Engineering and Bunova Cloud foundation

**Authorities:** `SYSTEM_ARCHITECTURE.md`, `DOMAIN_MAP.md`, `PLATFORM_SAAS_AND_TENANT_LIFECYCLE.md`, capability/platform-entitlement/offline/security contracts and P01 phase plan.

- [ ] `P01-001` Create repository application/workspace layout for Laravel backend/web, Flutter operational app(s), shared contracts/docs and tooling. **Accept:** boundaries are clear, build instructions work, no unnecessary microservices.
- [ ] `P01-002` Establish environment/config strategy for local/test/staging/production with secret-safe templates and validation.
- [ ] `P01-003` Bootstrap PostgreSQL/Redis/Reverb/object-storage local dependencies and health checks; document reproducible startup.
- [ ] `P01-004` Implement organization/brand/branch tenancy primitives with explicit organization/branch keys and global IDs.
- [ ] `P01-005` Implement user/staff identity separation, authentication foundation and session security suitable for web and registered devices.
- [ ] `P01-006` Implement roles/permissions/policies foundation with branch scoping and negative authorization tests.
- [ ] `P01-007` Implement device/register registry, registration/revocation, capability/mode metadata and secure device credential strategy.
- [ ] `P01-008` Implement capability registry/dependency validation and organization/branch configuration. **Contract:** `CAPABILITY_CONFIGURATION_CONTRACT.md`.
- [ ] `P01-009` Implement onboarding preset definitions without mode-specific forks; add dependency/disable-history tests.
- [ ] `P01-010` Establish canonical ID, money, currency, timezone/business-day, locale and audit value primitives.
- [ ] `P01-011` Establish module boundary conventions, domain command/use-case conventions, transaction/outbox policy and dependency rules.
- [ ] `P01-012` Establish versioned API conventions, error envelope, pagination/filtering, idempotency headers and correlation IDs.
- [ ] `P01-013` Establish audit event infrastructure per `AUDIT_EVENT_CONTRACT.md` and sensitive-action helpers.
- [ ] `P01-014` Establish queue/outbox/inbox infrastructure with idempotent job/event consumption.
- [ ] `P01-015` Establish structured logs/error tracking/metrics correlation and baseline health/readiness endpoints.
- [ ] `P01-016` Bootstrap Flutter architecture: routing, DI/state approach, localization, secure storage, Drift base DB, sync shell and design tokens.
- [ ] `P01-017` Implement registered-device bootstrap/auth handshake and branch/capability/config projection to Flutter.
- [ ] `P01-018` Establish web design-system shell and role-aware navigation with Arabic/English/RTL baseline.
- [ ] `P01-019` Establish test factories/fixtures with hard separation from production seed/demo data.
- [ ] `P01-020` Establish CI/self-hosted checks: backend tests/static/lint, Flutter analyze/tests, contract/schema validation, secret/dependency scans and path/reference checks.
- [ ] `P01-021` Measure baseline boot/API/local-DB/app-start performance and set first realistic budgets.
- [ ] `P01-022` Implement Platform Plan/Plan Version and Commercial Entitlement primitives separately from enabled capabilities. **Contract:** `PLATFORM_ENTITLEMENT_AND_SUBSCRIPTION_CONTRACT.md`. **Accept:** entitled ≠ enabled; plan history is versioned and explainable.
- [ ] `P01-023` Implement tenant trial/subscription lifecycle foundation and organization commercial state without coupling it to café customer Billing/Payments.
- [ ] `P01-024` Implement deterministic branch/device/capability commercial limit evaluation and explicit remediation behavior; downgrade never deletes existing data.
- [ ] `P01-025` Implement signed/versioned entitlement projection to registered operational devices with issued/expiry/grace metadata and server refresh/revocation hooks.
- [ ] `P01-026` Establish separately authorized Bunova Platform Admin shell for tenant/plan/entitlement/service administration; no café-owner role can acquire platform privileges through tenant configuration.
- [ ] `P01-027` Add foundational tests proving Platform Billing/entitlement records cannot be confused with café Bills/Payments, and proving capability use requires commercial entitlement + enabled capability + user/device authorization.
- [ ] `P01-AUDIT` Perform independent foundation audit: tenancy/auth/platform-vs-café boundaries/module boundaries/secrets/RTL/offline entitlement base/test quality; convert findings into canonical tasks and resolve P0/P1.
- [ ] `P01-GATE` Close engineering/Cloud foundation after suite/audit/docs/evidence pass.

---

# P02 — Catalog, pricing and menu truth

**Authorities:** `CATALOG_PRICING_AND_AVAILABILITY.md`, `MENUZA_INTEGRATION_CONTRACT.md`, financial/data/UX rules.

- [ ] `P02-001` Implement category/collection hierarchy, ordering, localization and archive semantics.
- [ ] `P02-002` Implement product master with stable identity, localized content, channel visibility and lifecycle/archive rules.
- [ ] `P02-003` Implement variants/sizes with stable IDs, SKU/barcode optional and branch/channel availability.
- [ ] `P02-004` Implement modifier groups/options with required/min/max/default constraints, variant applicability, price delta and recipe-effect metadata.
- [ ] `P02-005` Implement canonical units/unit conversions and validation needed by catalog/recipes/purchasing.
- [ ] `P02-006` Implement tax classification/reference model without embedding jurisdiction logic into product records.
- [ ] `P02-007` Implement base price lists and deterministic organization/branch/channel override precedence.
- [ ] `P02-008` Implement scheduled/daypart price rules with conflict/precedence explanation and tests.
- [ ] `P02-009` Implement availability engine combining active/channel/schedule/manual sold-out/station/capability and configured stock policy with reason codes.
- [ ] `P02-010` Implement product media storage/processing/reference strategy with safe fallback and ordering.
- [ ] `P02-011` Implement production-station routing metadata hooks without implementing P05 workflow prematurely.
- [ ] `P02-012` Build owner/admin catalog UX: category/product/variant/modifier editing, bulk status, search/filter, clear branch overrides and mobile-responsiveness.
- [ ] `P02-013` Build POS-optimized catalog projection/query/search/favorites metadata contract.
- [ ] `P02-014` Build catalog import preview/validation/commit/report for CSV/spreadsheet; no blind destructive overwrite.
- [ ] `P02-015` Add historical pricing snapshot tests proving later catalog changes do not rewrite transaction fixture totals.
- [ ] `P02-016` Define/implement Menuza publication projection/schema version and snapshot/checksum generation, but defer live integration to P12.
- [ ] `P02-017` Implement Flutter local catalog/config projection schema and version migration with stale-version indicator.
- [ ] `P02-AUDIT` Audit combinatorial modifiers, price precedence, availability reasons, branch isolation, RTL/admin UX and local projection; resolve findings.
- [ ] `P02-GATE` Close catalog/pricing phase.

---

# P03 — POS, orders, unified billing, payments and cash shifts

**Authorities:** `UNIFIED_BILLING_ENGINE.md`, `UNIFIED_BILLING_CONTRACT.md`, `POS_ORDERS_PAYMENTS_AND_SHIFTS.md`, financial/offline rules.

- [ ] `P03-001` Implement cart/order aggregates and explicit order channel/context model with global IDs and human pickup/order sequences.
- [ ] `P03-002` Implement server-side canonical line pricing from product/variant/modifier snapshots; reject invalid/stale combinations deterministically.
- [ ] `P03-003` Implement Bill/Billable Line model for product/service/time/access source types before source-specific modules use it.
- [ ] `P03-004` Implement money/tax/discount/service-charge/tip rounding and allocation engine with deterministic reconciliation tests.
- [ ] `P03-005` Implement discount/promotion primitive and manual discount permission/reason/audit; keep advanced bundles for later phase.
- [ ] `P03-006` Implement bill finalization state machine and immutable historical snapshots.
- [ ] `P03-007` Implement payment/tender domain for cash, external card/wallet/manual external and mixed tender with exact allocations.
- [ ] `P03-008` Implement cash tender amount/change and cash refund behavior.
- [ ] `P03-009` Implement provider-neutral external payment adapter interface/status model and fake provider; no real provider lock-in yet.
- [ ] `P03-010` Implement payment unknown/reconciliation state and duplicate/idempotency tests.
- [ ] `P03-011` Implement bill split by line/quantity/equal/custom amount and merge/transfer constraints with exact reconciliation.
- [ ] `P03-012` Implement pre-finalization item/order void/cancel permissions, reasons and production-effect placeholders/contracts.
- [ ] `P03-013` Implement post-payment partial/full refund linked to original bill/payment with compensating history.
- [ ] `P03-014` Implement printable/digital receipt snapshot and clearly marked reprint behavior; fiscal rendering waits for P15.
- [ ] `P03-015` Implement cash drawer/register and cashier shift lifecycle: float, movements, blind count, variance, approval and close blockers.
- [ ] `P03-016` Implement cash-in/out/petty-cash movement primitive with actor/reason and exact expected drawer projection.
- [ ] `P03-017` Build Flutter POS shell: category/product grid, search, cart, modifier flow, quantity/notes and customer/context hook.
- [ ] `P03-018` Build fast tender UX with exact cash/quick denominations/mixed payment, pending/failed/retry and receipt actions.
- [ ] `P03-019` Build rush-oriented interaction mode/config without forking commerce logic; validate tap count for common drink sale.
- [ ] `P03-020` Persist open local cart/order/shift state through app restart and enqueue allowed offline cash sale commands.
- [ ] `P03-021` Implement duplicate-safe offline cash-sale replay and authoritative acknowledgement; surface rejection/conflict explicitly.
- [ ] `P03-022` Add cashier/manager refund/void/override UX with permission escalation and reason capture.
- [ ] `P03-023` Add financial invariant tests: tax/discount/rounding/split/mixed tender/refund/idempotency/concurrency/historical price.
- [ ] `P03-024` Add basic sales/shift read models required for operator close and later analytics.
- [ ] `P03-AUDIT` Deep financial/POS/offline/UX audit; resolve all P0/P1 before gate.
- [ ] `P03-GATE` Close POS/billing/payments/cash phase with reproducible financial reconciliation evidence.

---

# P04 — Venue, floors, tables and service sessions

**Authorities:** `VENUE_TABLES_AND_SERVICE_SESSIONS.md`, `ORDER_SESSION_LIFECYCLES.md`, capability/offline/UX rules.

- [ ] `P04-001` Implement floor/zone resource structure with archive/history semantics and branch isolation.
- [ ] `P04-002` Implement table/room/cabin venue-resource types, capacity, status derivation and out-of-service behavior.
- [ ] `P04-003` Implement editable visual floor-plan metadata separate from operational identity/history.
- [ ] `P04-004` Implement Venue Session lifecycle with guest/customer/waiter context and open bill/order linkage.
- [ ] `P04-005` Implement open/add-order/reopen/close rules and blockers for unsettled bills/unresolved usage.
- [ ] `P04-006` Implement table/session transfer preserving history and billing/order links.
- [ ] `P04-007` Implement controlled session merge and guest/bill split semantics without rewriting production history.
- [ ] `P04-008` Implement waiter assignment/reassignment history and zone defaults.
- [ ] `P04-009` Implement service-request domain (waiter/bill/water/cleanup/configurable) with acknowledge/complete/assignment timestamps.
- [ ] `P04-010` Build owner/admin floor/resource editor with accessible responsive fallback outside drag canvas.
- [ ] `P04-011` Build waiter mobile resource grid/list showing availability/occupied/reserved/bill-requested and assigned zone.
- [ ] `P04-012` Build waiter session detail: orders, add items, service requests, bill handoff and readiness hooks.
- [ ] `P04-013` Build POS attach/select/open table/session workflow without slowing pure-counter preset.
- [ ] `P04-014` Implement realtime venue/session updates with durable server truth and reconnect refresh.
- [ ] `P04-015` Implement offline cached venue projection and controlled session/order operations permitted by policy; detect exclusive occupancy conflict.
- [ ] `P04-016` Add concurrency tests for double-open, transfer collision, close with unpaid bill and waiter reassignment.
- [ ] `P04-017` Implement configurable table/zone/room minimum-spend and cover-charge policies (per session/per guest/daypart) through canonical Billing, with customer-visible explanation, Menuza exposure where applicable, and permission/reason/audit for waiver. **Accept:** no hidden total or price mutation; split/merge/refund behavior remains reconcilable.
- [ ] `P04-AUDIT` Audit visual floor UX, session integrity, minimum-spend/cover behavior, realtime/offline collision and table-service workflow.
- [ ] `P04-GATE` Close venue/session phase.

---

# P05 — Production OS: bar, kitchen and KDS

**Authorities:** `PRODUCTION_BAR_KITCHEN_AND_KDS.md`, Restaurant interoperability contract, production lifecycle.

- [ ] `P05-001` Inspect live Restaurant System implementation/contracts before overlapping KDS/recipe/table-service work and document concrete reuse decision/evidence.
- [ ] `P05-002` If reuse extraction/API is needed, implement/version the shared production contract without importing unrelated restaurant complexity.
- [ ] `P05-003` Implement branch production stations, device/screen/print destinations, hours and fallback configuration.
- [ ] `P05-004` Implement product/variant/modifier to station routing resolution with deterministic multi-station fan-out.
- [ ] `P05-005` Implement production ticket/item snapshots and queued/preparing/ready/served lifecycle with allowed transitions.
- [ ] `P05-006` Implement cancellation after send, hold, remake/re-fire linkage and reason/waste hooks.
- [ ] `P05-007` Implement multi-station order partial-ready and handoff policy.
- [ ] `P05-008` Build Barista display optimized for drink modifiers, timers, source context and large touch targets.
- [ ] `P05-009` Build Kitchen/KDS surface or integrated shared KDS according to P05-001 decision.
- [ ] `P05-010` Implement shisha/dessert/other station extensibility without hardcoding only bar/kitchen.
- [ ] `P05-011` Implement production readiness notifications to waiter/POS/customer-display hooks.
- [ ] `P05-012` Implement realtime queue propagation plus durable refresh/reconnect behavior.
- [ ] `P05-013` Implement local station projection/offline transition queue where deployment policy permits, including duplicate transition idempotency.
- [ ] `P05-014` Implement queue/prepare/ready-to-serve/remake metrics source timestamps.
- [ ] `P05-015` Test mixed order fan-out, cancellation race, duplicate send, station outage/fallback, reconnect and partial readiness.
- [ ] `P05-AUDIT` Production usability/reuse/duplication/state-machine audit.
- [ ] `P05-GATE` Close Production OS phase.

---

# P06 — Inventory, recipes, procurement and waste

**Authorities:** `INVENTORY_RECIPES_PROCUREMENT_AND_WASTE.md`, data/financial integrity rules.

- [ ] `P06-001` Implement inventory item/store/location and append-oriented stock ledger with organization/branch scope.
- [ ] `P06-002` Implement base units, purchase/consumption conversions and precision rules with boundary tests.
- [ ] `P06-003` Implement recipe/BOM linking product variant/modifier effects to ingredients/packaging.
- [ ] `P06-004` Implement optional sub-recipe/prep/yield model only to café depth; avoid premature MRP.
- [ ] `P06-005` Decide/configure stock-consumption trigger policy and implement idempotent movement from sale/production facts.
- [ ] `P06-006` Implement cancellation/remake/refund compensating stock behavior; never delete original movement.
- [ ] `P06-007` Implement waste reasons, source/station/staff/cost snapshot and approval thresholds.
- [ ] `P06-008` Implement stock count session with expected snapshot, counted value, variance approval and adjustment movement.
- [ ] `P06-009` Implement store/branch transfer request-dispatch-receive-variance workflow with paired ledger facts.
- [ ] `P06-010` Implement supplier master and purchasing terms/contact basics.
- [ ] `P06-011` Implement PO draft/approve/send/reference/partial/full receiving and cancellation rules.
- [ ] `P06-012` Implement receiving into stock with purchase unit conversion, cost metadata and duplicate-receipt protection.
- [ ] `P06-013` Implement costing policy/projection and recipe theoretical COGS with snapshot/report semantics.
- [ ] `P06-014` Build admin stock dashboard, low-stock/advisory views, movement ledger drilldown and variance explanations.
- [ ] `P06-015` Build mobile/operational waste and count workflows with barcode/quick search where useful.
- [ ] `P06-016` Implement optional availability advisory/block integration without deleting catalog items or making stock projection the only truth.
- [ ] `P06-017` Test concurrent sale/receipt/count/transfer, negative stock policy, unit rounding, modifier replacement recipe and replay idempotency.
- [ ] `P06-018` Implement supplier invoice/operational payable facts from receiving/manual approved invoice references with due date/terms and traceable source.
- [ ] `P06-019` Implement supplier settlement/payment/credit movements including partial settlement and explicit adjustment/write-off permission/reason/audit; never directly edit supplier balance.
- [ ] `P06-020` Build supplier statement and due/overdue views reconciling opening balance + invoices/credits - payments to closing operational balance; label this as operational payables, not general-ledger accounting.
- [ ] `P06-021` Test duplicate supplier payment/reference, partial payment, return/credit, corrected invoice, branch scope and historical statement stability.
- [ ] `P06-AUDIT` Inventory ledger/COGS/waste/procurement/supplier-dues UX/data-integrity audit.
- [ ] `P06-GATE` Close inventory/procurement phase.

---

# P07 — Staff operations

**Authorities:** `STAFF_OPERATIONS.md`, security/privacy and audit rules.

- [ ] `P07-001` Implement staff profiles separate from login credentials with branch assignments and lifecycle/archive.
- [ ] `P07-002` Implement operational roles/templates while preserving granular permission policies from P01.
- [ ] `P07-003` Implement staff schedule/shift assignment primitives distinct from cashier financial shift.
- [ ] `P07-004` Implement clock-in/out/break records where attendance capability enabled; define offline device behavior.
- [ ] `P07-005` Implement station/zone/table/gaming assignment and routing/default implications.
- [ ] `P07-006` Implement quick operational actor switching/PIN or approved device unlock design without bypassing server authorization.
- [ ] `P07-007` Implement tip attribution/pooling policies and reporting hooks.
- [ ] `P07-008` Implement optional commission rule primitives only for operational use; do not create statutory payroll ERP.
- [ ] `P07-009` Implement staff meal/drink/complimentary allowance with explicit bill/stock/reason treatment.
- [ ] `P07-010` Build staff/admin roster, assignment and permission UX with clear branch scope.
- [ ] `P07-011` Build operational staff profile metrics using contextual service/production signals and avoid harmful simplistic ranking.
- [ ] `P07-012` Implement Payroll-Lite workforce-cost basis per staff/period: monthly, daily, hourly or shift-based wage with effective dates and branch assignment. **Accept:** rate changes preserve historical period explanation and do not pretend to implement statutory payroll compliance.
- [ ] `P07-013` Implement approved overtime/extra shifts, advances/loans, bonuses and deductions as auditable workforce-cost movements with reason/permission and correction history.
- [ ] `P07-014` Implement payout/settlement records and period summary showing earned basis, additions, deductions, advances and paid/outstanding amount; prohibit destructive balance edits.
- [ ] `P07-015` Build manager/owner Payroll-Lite UX and staff-cost analytics/export with privacy permissions; test attendance linkage, partial period, corrections and branch transfer cases.
- [ ] `P07-016` Implement versioned opening/handover/closing checklist templates scoped by branch/role/station/daypart with completion, note/reading and optional evidence requirements.
- [ ] `P07-017` Build operational checklist execution/exception/manager-verification UX and alerts; never duplicate canonical cash/stock/session facts as manually editable checklist truth.
- [ ] `P07-018` Test missed checklist, partial handover, offline completion/replay, template version change and manager override/audit.
- [ ] `P07-AUDIT` Staff permission/privacy/offline/Payroll-Lite/checklist/UX audit.
- [ ] `P07-GATE` Close staff operations phase.

---

# P08 — Timed Resource Engine

**Authorities:** `TIMED_RESOURCE_ENGINE.md`, unified billing, financial/time integrity skill/rules.

- [ ] `P08-001` Implement generic timed-resource types/resources with zone, capacity, operational and availability status.
- [ ] `P08-002` Implement rate-plan model: hourly/minute, minimum, grace, rounding, daypart/weekend, tier/multiplier and priority.
- [ ] `P08-003` Implement Timed Session lifecycle and immutable usage segments for active/pause/resume/transfer/end.
- [ ] `P08-004` Use monotonic elapsed-time tracking in Flutter and record wall-clock/server metadata for reconciliation.
- [ ] `P08-005` Implement deterministic rating service with applied rate-plan snapshot and reproducibility tests.
- [ ] `P08-006` Generate canonical `timed_usage` billable lines; forbid direct payment from timed domain.
- [ ] `P08-007` Implement session attachment to venue session/open bill and direct counter settlement context.
- [ ] `P08-008` Implement compatible resource transfer with usage-segment history and price-policy rules.
- [ ] `P08-009` Implement permission/reason/audit for duration/rate override, comp and force-end.
- [ ] `P08-010` Build resource grid/timer operational UI with available/reserved/in-use/paused/out-of-service states.
- [ ] `P08-011` Build rate/package selection and live estimated charge display while labeling estimate vs finalized rating.
- [ ] `P08-012` Persist active local timed sessions through app restart and controlled offline start/end according to policy.
- [ ] `P08-013` Implement exclusive-resource collision detection/reconciliation after offline concurrency.
- [ ] `P08-014` Implement stale/open-session alerts and manager resolution workflow without destructive history editing.
- [ ] `P08-015` Test boundaries: seconds/minutes rounding, midnight/daypart, pause, transfer, clock change/skew, duplicate end and concurrent start.
- [ ] `P08-AUDIT` Time/rating/offline/billing integrity and supervisor UX audit.
- [ ] `P08-GATE` Close Timed Resource Engine.

---

# P09 — Gaming OS

**Authorities:** `GAMING_AND_TIMED_SERVICES.md`, Timed Resource Engine and reservation/customer-value contracts.

- [ ] `P09-001` Implement gaming resource specialization for PS/Xbox/PC/VR and configurable future device types.
- [ ] `P09-002` Implement room/zone/player capacity/controller/accessory metadata and availability issue states.
- [ ] `P09-003` Implement gaming-specific rate dimensions such as player count/VIP/resource tier through timed rate plans, not a second calculator.
- [ ] `P09-004` Implement gaming session start flow selecting device, players, rate/package/customer and venue/bill context.
- [ ] `P09-005` Implement attached food/drink order creation from gaming session with production routing and same-bill behavior.
- [ ] `P09-006` Implement gaming packages/hour-credit integration hooks for P13 entitlement ledger.
- [ ] `P09-007` Implement maintenance/out-of-service/device issue flow and block incompatible session starts.
- [ ] `P09-008` Build Gaming Supervisor dashboard/grid with timers, alerts, order totals, pause/transfer/end and reservations.
- [ ] `P09-009` Build quick-start/extend/end workflows optimized for many simultaneous sessions.
- [ ] `P09-010` Implement stale-session/forgotten-open detection and manager review.
- [ ] `P09-011` Add optional game-title metadata only if it improves operations; keep nonessential library out of critical path.
- [ ] `P09-012` Define future IoT power-control adapter hook without coupling financial truth to smart plug state.
- [ ] `P09-013` Test multiplayer pricing, transfer, package partial usage, offline session, device outage and same-bill settlement.
- [ ] `P09-AUDIT` Gaming specialization vs timed-engine duplication, usability and billing audit.
- [ ] `P09-GATE` Close Gaming OS phase.

---

# P10 — Wi-Fi OS

**Authorities:** `WIFI_ACCESS_OS.md`, `WIFI_ROUTER_ADAPTER_CONTRACT.md`, security/integration rules.

- [ ] `P10-001` Implement router/provider registry, encrypted credentials, branch/SSID mapping and capability discovery.
- [ ] `P10-002` Implement provider-neutral router adapter interface and robust fake adapter/fixtures.
- [ ] `P10-003` Implement MikroTik adapter using supported secure management approach; document deployment/network prerequisites.
- [ ] `P10-004` Implement Wi-Fi package model for validity/active time/data/speed/concurrency/schedule/price.
- [ ] `P10-005` Implement entitlement/voucher lifecycle and secure credential/token generation.
- [ ] `P10-006` Implement provisioning idempotency and explicit pending/provisioning_failed/reconciliation states.
- [ ] `P10-007` Integrate paid Wi-Fi package as canonical billable line; provider provisioning never directly records revenue.
- [ ] `P10-008` Implement free entitlement rules/hooks from qualifying spend/loyalty/membership with source/reason.
- [ ] `P10-009` Implement voucher print/QR/digital display with time/data/speed terms and privacy-safe credential handling.
- [ ] `P10-010` Implement entitlement status/usage query/revoke/disconnect where adapter supports them.
- [ ] `P10-011` Implement periodic router state reconciliation and drift/error operator surface.
- [ ] `P10-012` Implement minimal captive-portal integration/session contract and Menuza/menu link, without creating a second CMS.
- [ ] `P10-013` Build cashier Wi-Fi sale/issue UX and manager package/router/status UX.
- [ ] `P10-014` Build router outage/provision failure resolution: retry/refund/reissue according to policy; never fake activation.
- [ ] `P10-015` Add security tests for credential exposure, authorization, forged voucher/context and adapter replay.
- [ ] `P10-016` Test time/data/quota expiry, duplicate provisioning, router unreachable/recovery, revocation and reconciliation.
- [ ] `P10-017` Implement controlled batch voucher/card generation by package + branch/router/SSID scope + quantity, preserving each voucher's lifecycle and generation actor/time. **Accept:** generating a batch alone creates no revenue.
- [ ] `P10-018` Implement printable/exportable Wi-Fi card sheets plus permissioned/audited reprint/export; clearly mark used/revoked/expired credentials and protect codes from ordinary unauthorized access.
- [ ] `P10-019` Test batch uniqueness, duplicate print/export, partial sale/activation, revocation, router drift and secure disposal/reissue scenarios.
- [ ] `P10-AUDIT` Wi-Fi commercial/security/router/batch-card resilience audit.
- [ ] `P10-GATE` Close Wi-Fi OS phase.

---

# P11 — Shisha and specialized café service

**Authorities:** `SHISHA_OPERATIONS.md`, catalog/production/inventory/session contracts.

- [ ] `P11-001` Model shisha products, flavors/mixes/head/extras using shared catalog primitives; avoid parallel catalog.
- [ ] `P11-002` Implement shisha recipe/coal/consumable stock effects through shared recipe/ledger.
- [ ] `P11-003` Configure shisha production station routing and state flow through shared Production OS.
- [ ] `P11-004` Implement remake/problem/waste linkage and reason capture.
- [ ] `P11-005` Implement recurring table service requests such as coal refill without forcing a charge unless configured.
- [ ] `P11-006` Build waiter/shisha-worker operational views and notifications.
- [ ] `P11-007` Implement optional paid service/extras as canonical billable lines.
- [ ] `P11-008` Add jurisdiction/config placeholder validation so expansion does not silently assume Egyptian rules elsewhere.
- [ ] `P11-AUDIT` Shisha reuse/stock/production/service UX audit.
- [ ] `P11-GATE` Close specialized café service phase.

---

# P12 — Menuza integration and online demand

**Authorities:** `MENUZA_INTEGRATION.md`, `MENUZA_INTEGRATION_CONTRACT.md`, ADR-005, integration skill.

- [ ] `P12-001` Inspect live Menuza repository/contracts before implementation; confirm exact current API/schema/deployment ownership and avoid checkpoint assumptions.
- [ ] `P12-002` Reconcile P02 publication schema with Menuza live capabilities; version any required contract changes explicitly.
- [ ] `P12-003` Implement Bunova catalog publication snapshot/delta job with branch/channel version/checksum and retry observability.
- [ ] `P12-004` Implement Menuza acknowledgement/checkpoint and full snapshot rebuild/reconciliation.
- [ ] `P12-005` Implement signed/opaque QR context issuance/resolution for branch/table/room/resource use.
- [ ] `P12-006` Implement Menuza order intake endpoint/consumer with authentication, schema version, idempotency and correlation.
- [ ] `P12-007` Revalidate item/modifier/price/availability/branch/capability/context on intake; return accepted/rejected/repricing semantics.
- [ ] `P12-008` Map accepted Menuza orders into canonical Bunova order/venue/production/billing contexts without special financial fork.
- [ ] `P12-009` Implement online pickup order context, promised/ready/pickup status and pickup token.
- [ ] `P12-010` Implement QR table order context linking to live/created venue session according to policy.
- [ ] `P12-011` Implement online-delivery entry context only to Bunova operational boundary; do not build full fleet unless separately scoped.
- [ ] `P12-012` Implement payment-reference verification ownership per chosen Menuza/provider mode; handle ambiguous payment/order timeout safely.
- [ ] `P12-013` Implement customer-safe status projection/callback plus query reconciliation path.
- [ ] `P12-014` Implement cancel/reject/refund status propagation contract.
- [ ] `P12-015` Build owner integration health/reconciliation dashboard: publication drift, failed orders, callbacks, unknown payments.
- [ ] `P12-016` Test duplicate submit, timeout-after-accept, stale price, sold-out, invalid QR, closed branch, callback outage, payment ambiguity and snapshot drift.
- [ ] `P12-017` Run cross-repository contract compatibility tests/evidence against live Menuza version.
- [ ] `P12-AUDIT` Independent integration ownership/idempotency/security/UX reconciliation audit.
- [ ] `P12-GATE` Close Menuza integration phase.

---

# P13 — Customers, loyalty, memberships, promotions, bundles and stored value

**Authorities:** `CUSTOMERS_LOYALTY_MEMBERSHIPS_AND_VALUE.md`, unified billing, privacy and idempotency rules.

- [ ] `P13-001` Implement minimal customer profile/search/merge policy with phone/identifier normalization and branch/org scope.
- [ ] `P13-002` Implement consent/preferences model separating transactional needs from marketing consent.
- [ ] `P13-003` Implement customer order history/favorite/repeat-order read model with privacy/permission constraints.
- [ ] `P13-004` Implement append-oriented loyalty points/stamps ledger and deterministic balance projection.
- [ ] `P13-005` Implement earn rules by spend/item/category/visit with exclusions, expiry and branch applicability.
- [ ] `P13-006` Implement reward redemption with idempotency, bill allocation and refund reversal semantics.
- [ ] `P13-007` Implement membership plans, validity/renewal state and included benefits/discount eligibility.
- [ ] `P13-008` Implement prepaid coffee/item credit ledger and consumption.
- [ ] `P13-009` Implement prepaid gaming-hour credit ledger integrated with rated timed usage.
- [ ] `P13-010` Implement combined bundles (gaming + drinks / coffee packages) with transparent entitlement/revenue allocation rules.
- [ ] `P13-011` Implement gift-value issuance/redemption/refund/expiry ledger after confirming accounting/legal treatment for launch market.
- [ ] `P13-012` Build POS customer lookup/scan, benefit explanation, repeat/favorite order and redemption UX.
- [ ] `P13-013` Build owner/admin program builder with guardrails against conflicting/abusive rules.
- [ ] `P13-014` Build customer program balance/history and support correction with permission/audit rather than direct edit.
- [ ] `P13-015` Implement notification hooks for transactional membership/reward events respecting consent/channel rules.
- [ ] `P13-016` Test duplicate earn/redeem, refund, expiry boundary, partial package consumption, concurrent devices and customer merge.
- [ ] `P13-017` Implement commercial coupon/promotion rule set for café use cases (happy hour, coffee+bakery combo, gaming+drink/meal, BOGO/second-item, student/member promotions) using canonical Billing allocations and deterministic precedence; do not mutate catalog prices invisibly.
- [ ] `P13-018` Build promotion eligibility/explanation/admin UX and test stacking conflicts, schedule/daypart, channel/branch scope, refunds and entitlement combinations.
- [ ] `P13-019` Implement optional customer/corporate house-account approval, credit limit/terms, allowed branch/channel scope and active/blocked status.
- [ ] `P13-020` Implement house-account charge and settlement ledger movements linked to finalized bills/payments; support partial settlement, refund/credit and statement reconciliation without mutable balance editing.
- [ ] `P13-021` Build POS eligibility/limit/overdue explanation and controlled house-account tender; over-limit/manual adjustment/write-off requires configured permission/reason/audit.
- [ ] `P13-022` Build customer/corporate account statement, aging/due view and settlement UX; validate launch-market accounting/tax/legal treatment before production.
- [ ] `P13-023` Test concurrent credit use, limit race, refund, partial settlement, block/unblock, merged customer and historical statement stability.
- [ ] `P13-AUDIT` Stored-value/promotion/house-account/privacy/financial and POS usability audit.
- [ ] `P13-GATE` Close customer programs phase.

---

# P14 — Reservations and capacity

**Authorities:** `RESERVATIONS_AND_CAPACITY.md`, venue/timed/gaming/customer/payment rules.

- [ ] `P14-001` Implement reservable resource/resource-class abstraction for table/room/gaming/timed resources.
- [ ] `P14-002` Implement reservation lifecycle with time range, capacity, fixed/deferred assignment and conflict validation.
- [ ] `P14-003` Implement setup/cleanup buffers and no-overlap constraints with timezone/DST tests.
- [ ] `P14-004` Implement deposit/payment-reference linkage and explicit application/refund/no-show treatment.
- [ ] `P14-005` Implement arrival/check-in conversion to Venue Session or Timed Session preserving reservation history.
- [ ] `P14-006` Implement cancellation/no-show/late-arrival policies and permission/audit.
- [ ] `P14-007` Implement server-authoritative create/reschedule; define limited offline cached/read/check-in behavior.
- [ ] `P14-008` Build owner calendar/timeline/resource availability UX with mobile fallback.
- [ ] `P14-009` Build cashier/gaming supervisor arrival/check-in and walk-in conflict UX.
- [ ] `P14-010` Implement reminder/confirmation/cancellation notification jobs independent of reservation truth.
- [ ] `P14-011` Test concurrent booking, deferred assignment, deposit refund, no-show, resource outage and check-in race.
- [ ] `P14-AUDIT` Capacity/conflict/payment/UX audit.
- [ ] `P14-GATE` Close reservations phase.

---

# P15 — Fiscal, expenses and financial controls

**Authorities:** `FISCAL_EXPENSES_AND_FINANCIAL_CONTROLS.md`, `FISCAL_ADAPTER_CONTRACT.md`, financial/fiscal rules.

- [ ] `P15-001` Revalidate current official Egyptian ETA eReceipt requirements/API/security/item coding/POS registration/document/return semantics before coding; record implementation-era source/version evidence.
- [ ] `P15-002` Implement normalized immutable Fiscal Transaction snapshot from finalized Bunova bill/refund.
- [ ] `P15-003` Implement fiscal organization/branch/register identity/config validation and secret-safe credentials.
- [ ] `P15-004` Implement ETA adapter mapping/validation behind Fiscal contract; provider schema never leaks into Billing core.
- [ ] `P15-005` Implement durable fiscal queue, stable submission identity/hash, retry/backoff and response history.
- [ ] `P15-006` Implement timeout/unknown query-reconciliation before duplicate-risk retry according to current ETA contract.
- [ ] `P15-007` Implement accepted/rejected/retry/reconciliation states and manager diagnostics with human-readable error mapping.
- [ ] `P15-008` Implement fiscal return/correction flow linked to Bunova refund and original external fiscal document.
- [ ] `P15-009` Implement offline-sale pending fiscal behavior only as legally/configurationally permitted; never fake accepted status.
- [ ] `P15-010` Implement operational expense categories/records/attachments/approval and cash-shift movement linkage.
- [ ] `P15-011` Implement day-close aggregate/summary reconciling cash/external tenders/refunds/voids/expenses/fiscal exceptions and open operational blockers.
- [ ] `P15-012` Implement external tender settlement/reference reconciliation hooks without full accounting ERP.
- [ ] `P15-013` Build fiscal/financial exception dashboards, filters, retry/reconcile actions and audit detail.
- [ ] `P15-014` Build close-of-day UX with blockers/warnings, variance/fiscal summaries and manager override policy.
- [ ] `P15-015` Add deterministic fixtures for accepted/rejected/duplicate/timeout/return schema and sandbox/official test evidence where available.
- [ ] `P15-016` Security-review fiscal credentials, logs, payload redaction and authorization.
- [ ] `P15-AUDIT` Financial/fiscal independent audit; zero open P0/P1.
- [ ] `P15-GATE` Close fiscal/financial controls phase.

---

# P16 — Owner control center and analytics

**Authorities:** `ANALYTICS_AND_OWNER_CONTROL_CENTER.md`, data ownership and UX standards.

- [ ] `P16-001` Define metric dictionary/formulas and dimensions before dashboard implementation; prevent same metric having conflicting definitions.
- [ ] `P16-002` Implement analytics projection pipeline/read models that never become operational write authority.
- [ ] `P16-003` Implement Live Café branch status: tables/rooms, timed/gaming resources, production queues, pickup, staff, device/router/provider exceptions.
- [ ] `P16-004` Implement sales/revenue/net/tax/discount/tip/refund/average-ticket/item/order metrics by branch/channel/daypart.
- [ ] `P16-005` Implement product/category/variant/modifier mix and attach-rate reporting.
- [ ] `P16-006` Implement theoretical COGS/gross margin/waste/variance/stock-risk metrics with explicit assumptions.
- [ ] `P16-007` Implement production/service timing and throughput metrics.
- [ ] `P16-008` Implement timed/gaming utilization, revenue/hour, idle time, package use, reservations/no-show and downtime metrics.
- [ ] `P16-009` Implement customer repeat/retention/loyalty/membership utilization metrics within privacy policy.
- [ ] `P16-010` Implement cash/refund/discount/complimentary/stock/session anomaly signals as explainable review signals, not accusations.
- [ ] `P16-011` Build owner mobile-first overview plus dense desktop drilldowns; every chart has tabular/accessibility alternative where needed.
- [ ] `P16-012` Build branch comparison and date/daypart/filter/export flows with permission-safe data scope.
- [ ] `P16-013` Implement scheduled report/export foundation with async job and secure expiring download.
- [ ] `P16-014` Validate metric projections against source ledgers on deterministic fixture dataset.
- [ ] `P16-AUDIT` Analytics correctness, performance, privacy and non-generic UX audit.
- [ ] `P16-GATE` Close analytics/control-center phase.

---

# P17 — Offline-first sync and resilience hardening

**Authorities:** `OFFLINE_SYNC_ARCHITECTURE.md`, `OFFLINE_SYNC_CONTRACT.md`, `PLATFORM_ENTITLEMENT_AND_SUBSCRIPTION_CONTRACT.md`, offline rules.

- [ ] `P17-001` Inventory every operational command and explicitly classify offline-allowed/online-required/conditional.
- [ ] `P17-002` Finalize local Drift projection schemas and migration/version compatibility for all enabled operational modules.
- [ ] `P17-003` Implement atomic local mutation + durable outbox for each offline-allowed command family.
- [ ] `P17-004` Implement authenticated batch/single command ingestion with idempotency, expected-version validation and structured conflict response.
- [ ] `P17-005` Implement server-to-device delta/checkpoint feed or equivalent authoritative refresh strategy with versioning.
- [ ] `P17-006` Implement dependency-safe upload ordering and retry/backoff without blocking unrelated safe commands unnecessarily.
- [ ] `P17-007` Implement conflict UI/resolution for exclusive resource, stale config, rejected permissions and financial reconciliation-required cases.
- [ ] `P17-008` Implement local sync health/status surface: last contact, backlog count/age, conflicts and retry/support export.
- [ ] `P17-009` Implement device/app restart recovery for open shift, orders, venue sessions, timed sessions and queued production work.
- [ ] `P17-010` Implement stale/revoked device behavior, credential expiry and controlled queued-command acceptance policy.
- [ ] `P17-011` Test offline catalog/price/capability stale behavior and server rejection/repricing.
- [ ] `P17-012` Test two-device table/resource/session collisions and deterministic reconciliation.
- [ ] `P17-013` Test disconnect during cash sale, external payment initiation, production change, gaming end, shift close and fiscal queue.
- [ ] `P17-014` Test duplicate replay, app crash between local commit/send/ack, server retry and out-of-order responses.
- [ ] `P17-015` Load-test backlog replay after long outage and prevent UI starvation/server thundering herd.
- [ ] `P17-016` Add chaos scenarios for Redis/Reverb/API partial outage proving durable truth does not depend on sockets.
- [ ] `P17-017` Harden offline entitlement/grace behavior: signed projection expiry, bounded grace, plan downgrade/suspension during open shift/session, eventual server reconciliation and clear operator state. **Accept:** no stranded accepted money/fiscal work and no indefinite offline license bypass.
- [ ] `P17-AUDIT` Independent sync/data-loss/conflict/resilience/entitlement-grace audit.
- [ ] `P17-GATE` Close offline/resilience phase only with convergence evidence.

---

# P18 — Hardware, peripherals and operational assets

**Authorities:** `HARDWARE_NOTIFICATIONS_AND_ASSETS.md`, integration/security rules.

- [ ] `P18-001` Implement printer abstraction/discovery/config and supported 58/80mm receipt printing with locale/RTL/fiscal template needs.
- [ ] `P18-002` Implement print job status/retry/duplicate-reprint marking and operator-visible failure.
- [ ] `P18-003` Implement kitchen/bar printer routing where enabled without duplicating KDS ticket truth.
- [ ] `P18-004` Implement cash-drawer trigger only through authorized register actions and record supported drawer-open audit events.
- [ ] `P18-005` Implement barcode scanner keyboard-wedge/input flow with deterministic POS focus and duplicate scan handling.
- [ ] `P18-006` Implement customer display transport/state: cart, totals, payment status, pickup/loyalty/menu QR and privacy reset.
- [ ] `P18-007` Harden KDS/bar/gaming display device modes, heartbeat/version/sync indicators and kiosk/lock guidance where supported.
- [ ] `P18-008` Implement operational asset registry/status/warranty/maintenance history for coffee equipment, gaming, network and peripherals.
- [ ] `P18-009` Implement device/peripheral health diagnostics and support bundle with redaction.
- [ ] `P18-010` Implement notification abstraction/providers/jobs and in-app/realtime alert center with dedup/acknowledgment.
- [ ] `P18-011` Implement operational alerts: router/printer/device/sync/fiscal/payment/stale session/stock threshold as supported.
- [ ] `P18-012` Validate payment terminal/SoftPOS adapter integration path against P03 contract without hard-locking vendor.
- [ ] `P18-013` Define optional IoT power-control experimental adapter behind feature flag; never gate billing truth on it.
- [ ] `P18-AUDIT` Hardware failure/retry/privacy/security/field-operator UX audit.
- [ ] `P18-GATE` Close hardware/integration hardening phase.

---

# P19 — Premium UX, content, accessibility, localization and performance

**Authorities:** `UX_QUALITY_STANDARD.md`, `DESIGN_SYSTEM_AND_BRAND.md`, `LOCALIZATION_ACCESSIBILITY_AND_INPUT.md`, `SURFACE_MAP.md`, UX/content rules.

- [ ] `P19-001` Finalize production brand token values after contrast/venue-lighting testing; keep semantic token names stable.
- [ ] `P19-002` Complete shared web component inventory and replace unjustified one-off patterns in operational/admin flows.
- [ ] `P19-003` Complete Flutter component inventory/tokens and align semantics with web while preserving native ergonomics.
- [ ] `P19-004` Audit every high-frequency POS flow for tap/keyboard count, focus, scanner behavior and unnecessary modal/confirmation friction.
- [ ] `P19-005` Audit waiter flows one-handed/mobile and production/gaming flows for distance readability/large targets.
- [ ] `P19-006` Ensure every scoped surface implements loading/skeleton, first-empty, filtered-empty, validation, error, offline, stale, denied, conflict, pending/retry, success and destructive states where applicable.
- [ ] `P19-007` Complete Arabic translations/content review by domain, including errors, receipts, fiscal messages, setup help and notifications.
- [ ] `P19-008` Complete English copy review; eliminate placeholder/lorem/generic AI text and inconsistent terminology.
- [ ] `P19-009` Run full RTL visual/interaction audit across web, Flutter, printing/customer display and mixed bidi codes/numbers.
- [ ] `P19-010` Run accessibility audit: contrast, focus order, semantics/labels, keyboard, reduced motion, text scaling and non-color/non-sound alternatives.
- [ ] `P19-011` Run responsive audit for café owner/admin and Bunova Platform Admin/public website surfaces from phone through desktop; provide usable alternative to wide tables/canvas controls.
- [ ] `P19-012` Add targeted visual regression snapshots for critical shells/states in Arabic/English/light/dark/responsive contexts.
- [ ] `P19-013` Measure and optimize POS product search/cart/tender responsiveness and Flutter app start/local DB migration.
- [ ] `P19-014` Measure and optimize admin/control-center query/load performance with realistic branch data volumes.
- [ ] `P19-015` Audit animations/feedback for functional value and reduced-motion behavior; remove distracting/slow motion.
- [ ] `P19-016` Conduct anti-generic UX review across all major operational, café-admin, platform-admin and public-commercial surfaces; convert every poor/basic/CRUD-like flow into canonical remediation tasks and close P0/P1.
- [ ] `P19-AUDIT` Independent premium UX/content/accessibility/performance audit.
- [ ] `P19-GATE` Close product-experience hardening phase.

---

# P20 — Security, audit, full verification and operational acceptance

**Authorities:** `SECURITY_PRIVACY_AND_ABUSE.md`, `TEST_STRATEGY.md`, `AUDIT_STRATEGY.md`, platform entitlement contract, all other contracts/rules.

- [ ] `P20-001` Refresh threat model against implemented architecture and enumerate attack/abuse paths by surface/integration.
- [ ] `P20-002` Run tenant/organization/branch isolation test matrix across every domain/API/read model/export.
- [ ] `P20-003` Run granular authorization negative matrix for sensitive actions and verify UI hiding is not relied upon.
- [ ] `P20-004` Review staff/device authentication, session expiry, quick unlock/biometric/PIN design and stolen/revoked-device behavior.
- [ ] `P20-005` Run webhook/provider authentication/replay/idempotency security tests for payment/Menuza/fiscal/router/notifications/platform subscription provider.
- [ ] `P20-006` Run secret scan, dependency vulnerability scan, static analysis and configuration hardening review.
- [ ] `P20-007` Review logs/audit/support bundles for secrets/PII/payment/router/fiscal/platform credential leakage.
- [ ] `P20-008` Test CSRF/XSS/injection/mass-assignment/file upload/export/download authorization and API rate/abuse protections.
- [ ] `P20-009` Run financial abuse scenarios: refund/void/discount/complimentary/drawer/shift variance/duration/rate/stock/house-account/supplier/workforce-cost manipulation.
- [ ] `P20-010` Verify immutable/compensating history invariants for bill/payment/cash/stock/time/loyalty/gift/house-account/supplier/workforce/fiscal/platform-subscription records as applicable.
- [ ] `P20-011` Run full backend/Flutter/contract/UI/visual suites on production-like configuration.
- [ ] `P20-012` Run offline/chaos/resilience suite from P17 and integration outage fixtures from P12/P15/P18.
- [ ] `P20-013` Run realistic load/performance test for branch rush, multi-branch owner read models, queue workers, sync replay and production realtime.
- [ ] `P20-014` Run data migration/schema upgrade test from supported previous build/app local DB versions.
- [ ] `P20-015` Search repo/runtime for TODO/FIXME/HACK/mock/demo/placeholder/skipped/disabled tests and classify every hit; resolve production-risk items.
- [ ] `P20-016` Perform full independent product audit against every domain/phase authority, excluding intentionally future/non-goal scope.
- [ ] `P20-017` Resolve all P0/P1 findings through canonical tasks; document accepted P2 only with owner/rationale where allowed.
- [ ] `P20-018` Freeze release-candidate API/event/local-sync schema compatibility and migration expectations.
- [ ] `P20-019` Verify platform-operator authorization/support-access boundaries, tenant lifecycle controls, entitlement override audit and separation between Platform Billing and Café Billing.
- [ ] `P20-AUDIT` Final security/data/financial/UX/platform/operational acceptance review signs off evidence.
- [ ] `P20-GATE` Close full verification gate; launch preparation may start only after zero blocker findings.

---

# P21 — Onboarding, SaaS commercialization, migration, support and production launch

**Authorities:** `ONBOARDING_AND_SETUP.md`, `MIGRATION_SUPPORT_AND_LAUNCH.md`, `OBSERVABILITY_AND_OPERATIONS.md`, `PLATFORM_SAAS_AND_TENANT_LIFECYCLE.md`, release workflow.

- [ ] `P21-001` Implement production onboarding wizard: organization/locale/branch/preset/capabilities/catalog/tax/venue/stations/device/staff/payments/integrations/readiness.
- [ ] `P21-002` Implement setup-readiness validator based on enabled capabilities and actual configuration, not clicked-step percentage.
- [ ] `P21-003` Implement safe test-sale/hardware/integration checks that do not pollute real revenue/fiscal data.
- [ ] `P21-004` Finalize CSV/spreadsheet imports for catalog/customers/opening stock/suppliers/approved stored value with preview/validation/result report.
- [ ] `P21-005` Document scope and process for legacy financial-history migration separately; do not pretend generic CSV fully migrates ledgers/fiscal history.
- [ ] `P21-006` Create contextual help/operator quick-start for cashier, waiter, barista/kitchen, gaming supervisor, manager and owner.
- [ ] `P21-007` Create support/diagnostics workflow, redacted support bundle and escalation permissions.
- [ ] `P21-008` Finalize monitoring dashboards/alerts for API/DB/Redis/queues/Reverb/sync/payment/fiscal/Menuza/router/device/business-health signals.
- [ ] `P21-009` Write and verify runbooks for backend/database/Redis/Reverb/payment/ETA/Menuza/router/printer/device/sync/security incidents.
- [ ] `P21-010` Configure automated backups/retention/encryption and perform documented restore drill into isolated environment.
- [ ] `P21-011` Revalidate current ETA/payment/privacy/legal/commercial configuration immediately before production launch.
- [ ] `P21-012` Perform production-like migration dry run and measure downtime/compatibility; prove supported Flutter/backend version skew.
- [ ] `P21-013` Establish release versioning/changelog/rollback-or-roll-forward plan and app distribution/signing/update strategy.
- [ ] `P21-014` Pilot internally/demo branch with real hardware and no fake production data; close findings.
- [ ] `P21-015` Pilot one controlled real café shift including counter/table/production and any enabled gaming/Wi-Fi/fiscal capabilities; reconcile every transaction/shift.
- [ ] `P21-016` Execute deliberate internet-outage/reconnect during pilot and verify no sale/session/shift data loss or duplicate side effect.
- [ ] `P21-017` Pilot multi-device and multi-role concurrency, printer/router/payment/fiscal failure recovery and support diagnostics.
- [ ] `P21-018` Review pilot support burden/operator feedback and convert real issues into canonical tasks; resolve launch blockers.
- [ ] `P21-019` Verify no production demo users/orders/revenue/secrets/test fiscal endpoints or unsafe default credentials/config remain.
- [ ] `P21-020` Run final launch checklist, smoke suite, backup snapshot, monitoring readiness and rollback decision gate.
- [ ] `P21-021` Execute progressive production rollout with active monitoring and reconciliation checks; record release evidence.
- [ ] `P21-022` Complete post-launch first-business-day reconciliation: money, shifts, stock movements, timed sessions, Wi-Fi, production, Menuza and fiscal queues.
- [ ] `P21-023` Complete post-launch stabilization review and ensure every discovered issue is represented only in this canonical TODO until resolved.
- [ ] `P21-024` Finalize commercial Plan Versions and Arabic/English plan matrix: branch/device allowances, premium capabilities, integrations/support tier and pricing rules; plan changes never rewrite prior subscription periods.
- [ ] `P21-025` Implement trial, subscription period, renewal, upgrade/downgrade, cancellation, past-due/grace, suspension and reactivation flows with explicit lifecycle history.
- [ ] `P21-026` Implement provider-neutral Bunova SaaS payment/subscription adapter plus auditable manual payment option where launch go-to-market requires it; handle duplicate/out-of-order/unknown provider states by reconciliation.
- [ ] `P21-027` Build Bunova Platform Admin production surface for tenants, plan/subscription/entitlement usage, commercial exceptions, service health, support cases, lifecycle operations and platform audit.
- [ ] `P21-028` Implement controlled platform support-access grants/diagnostics with actor, reason/case reference, scope/expiry and audit; avoid routine direct DB editing.
- [ ] `P21-029` Implement tenant downgrade/suspension/reactivation/export/closure/retention workflows that preserve operational/fiscal history and safely settle/sync accepted work.
- [ ] `P21-030` Build Bunova public Arabic/English commercial website and acquisition/onboarding surface: product/archetypes/capabilities, current pricing/plan comparison, lead/contact or signup/trial path, legal/privacy/help entry points, responsive premium UX, analytics and post-K00 SEO/Content authority compliance.
- [ ] `P21-031` Test entitlement limit races, trial expiry, offline grace, downgrade with over-limit resources, past-due recovery, subscription payment ambiguity and cross-tenant Platform Admin authorization.
- [ ] `P21-032` Reconcile Bunova-company SaaS subscription charges independently from every pilot café's own sales; prove no report/ledger mixes Platform Billing with Café Billing.
- [ ] `P21-033` Update commercial/support documentation, plan-change communication, subscription exception runbooks and tenant offboarding instructions.
- [ ] `P21-GATE` Production acceptance: Bunova is launch-complete only after café pilot/rollout/reconciliation, SaaS commercial/platform operations, support/restore/security evidence pass and no P0/P1 remains.

---

# Recurring operational rules (not a second backlog)

These rules apply while executing the phase tasks above and do not become standalone recurring checkbox tasks:

- After every completed task: update this `TODO.md` immediately and keep stable docs/contracts synchronized if truth changed.
- After each phase/wave: run the required heavier test suite and independent audit; findings return here as canonical tasks.
- Before external-integration work: re-check the live provider/product contract/version; do not trust old planning details as current API truth.
- Before starting/resuming a session: inspect live repository state and interrupted work.
- Never delete/shorten planning intent merely to make the TODO look smaller; normalize duplication only when semantics are demonstrably preserved.
- Never mark future tasks as defects during audits simply because they are still open; flag only contradictions, regressions, missing canonical work or unacceptable current-state shortcuts.
