# K00 Tooling / Host Evidence Status

This file records evidence availability; it is not task state and cannot replace `TODO.md`.

| Capability / evidence | Status in this ChatGPT/GitHub-connector session | Evidence / rule |
| --- | --- | --- |
| Inspect Bunova live GitHub repository and write `main` | PASS | GitHub connector read/write actions succeeded and every integration chunk was published by fast-forward update. |
| Inspect live `Elwazeery/AI-ExcutionKit` source, VERSION, installer, schemas and validators | PASS | Current live source HEAD inspected/pinned: `5a9eda4ab2159f93cca9867b492fe9ceb65ce261`, VERSION 3.0.0. The source commit reports `npm run check` with 346 tests / zero failures on Linux / Node v24.19.0; target-project/host acceptance remains separate. |
| Pin exact runtime gitlink/submodule in Bunova | PASS | Bunova tree uses canonical `.executionkit/runtime/kit` at the exact current inspected source commit; `.gitmodules`, `.gitignore`, discovery exclusion and config `installation.runtimePath` agree. |
| Reconcile latest runtime/State isolation repair | PASS (STATIC ONLY) | The pin includes the current upstream repair for isolated runtime policy/schema/fleet resolution and protection against derived State writes into installed runtime source. Bunova already uses supported canonical `.executionkit/runtime/kit`; actual project execution remains host evidence. |
| Reconcile v3 project-surface schemas/docs against pinned source | PASS (STATIC ONLY) | Required SEO/telemetry schemas and v3/SEO architecture docs are present; current `validate-v30-project.mjs` resolves isolated-runtime bin/lib/schema/capability authorities from the configured runtime. |
| Reconcile native task projection contract | PASS (STATIC ONLY) | Projector is hash-bound to `TODO.md`/`TODO_ARCHIVE.md`, preserves native source markers, projects open `[ ]` tasks to executable `READY`, maps `[~]` to reviewable `IMPLEMENTED`, omits schema-invalid empty enum fields/root metadata, rejects duplicate IDs and enforces deterministic sequential dependencies. |
| Reconcile Agent OS manifest/routing | PASS (STATIC ONLY) | Dedicated K00 integrator route exists; explicit audit actions retain higher-priority QA routing; native resources remain registered/reachable by declared routes/supplements/fallback. Runtime `agent-doctor` evidence remains required. |
| Install Antigravity project surfaces | PASS (STATIC ONLY) | Pinned plugin bundle exists under `.agents/plugins/executionkit`; workspace hook, MCP config, entry rule, all native role adapters and all native skill adapters are present. Final `.agents/manifest.json` follows the official writer format and uses `rule.todo-authority` as the adapter source. |
| Activate/discover Antigravity plugin and hook in the actual editor | NOT_RUN | Repository files cannot prove editor discovery, hook invocation, restart pickup or host-native adapter behavior. Verify after pulling/initializing the project on the actual Antigravity workstation. |
| Validate adapter configuration | PASS (STATIC ONLY) | `executionkit.adapters.json` uses schema 1.0.0 and an object-valued `adapters` registry as required by the pinned `loadAdapterConfig()` implementation. Runtime adapter detection/collection remains host evidence. |
| Validate State OS configuration | PASS (STATIC ONLY) | State paths remain under `.executionkit/`, heartbeat is within the pinned validator range and external memory is advisory/live-revalidated with task-state authority forbidden. |
| Validate CI policy configuration | PASS (STATIC ONLY) | Declared policy is self-hosted/manual, forbids GitHub-hosted and push autoruns, limits one project runner per device and declares macOS/windows. At M0 no workflow exists yet; the pinned validator treats that as a warning, and orchestration is planned in `P01-020`. |
| Materialize/init private submodule on a normal authenticated workstation | NOT_RUN | Requires a host checkout with GitHub authentication; gitlink presence is not execution evidence. |
| Run Bunova task projector with Node in an initialized project checkout | NOT_RUN | Repository code was inspected statically here; direct host execution evidence is still required. |
| Run ExecutionKit doctor / execution / agent / deep-integration / quality / SEO / CI / v3 validators | NOT_RUN | The available execution container cannot resolve `github.com`; no validator PASS is inferred from static conformance or upstream source tests. Run against the exact pinned runtime on an authenticated runtime-capable host. |
| Run representative Agent routing dry-runs | NOT_RUN | Must prove ordinary K00 resolves to the ExecutionKit integrator, `K00-019` resolves independently to QA audit, and representative product task families resolve the expected native specialists. |
| State checkpoint -> rehydrate -> zero-context Amnesia Test | NOT_RUN | Required before K00 activation; repository config is insufficient evidence. |
| ExecutionKit MCP server handshake and representative read-only/tool calls | NOT_RUN | Requires MCP-capable host integration after project/plugin discovery. |
| Memory MCP | NOT_CONFIGURED | Optional/advisory; absence must not block canonical execution and it may never own task state. |
| macOS/Windows self-hosted project runner registration | NOT_CONFIGURED | Project policy is declared; runner registration is host/org infrastructure evidence and is not required to fabricate M0 execution evidence. |
| Laravel Boost adapter | NOT_APPLICABLE_YET | No Laravel runtime exists at M0. Reassess in P01 after scaffold exists. |
| Dart/Flutter MCP adapter | NOT_APPLICABLE_YET | No Flutter runtime exists at M0. Reassess in P01 after scaffold exists. |
| Printer/router/payment/ETA/physical-device evidence | NOT_APPLICABLE_TO_K00 | Belongs to the relevant product/integration/launch phases, not kit installation acceptance. |

## Remaining K00 host acceptance boundary

Repository-side deep integration is installed and statically reconciled. The remaining acceptance work must run from a real initialized checkout using the exact pin. It includes submodule materialization, task projection generation, the applicable project validators, representative Agent routing, State checkpoint/rehydrate/Amnesia, Antigravity plugin/hook discovery, and ExecutionKit MCP handshake/tool calls. Failures discovered there return to canonical `TODO.md`; they are not hidden in this evidence document.

## Acceptance rule

`PASS (STATIC ONLY)` proves repository structure/configuration, not executable runtime behavior. No `NOT_RUN`, `NOT_CONFIGURED` or `NOT_APPLICABLE_YET` row may be silently rewritten as PASS. `K00-019` / activation may close only after every K00-required runtime/host row has direct current evidence or is explicitly proven non-blocking by the pinned kit policy.
