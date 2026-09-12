# Bunova Agent OS Routing Matrix

This is a deterministic design/evidence aid. `.agents/manifest.json` is the machine registry and `TODO.md` remains task authority.

| Task shape | Expected primary route | Core specialist/context |
| --- | --- | --- |
| K00 integration/reconciliation | `route.executionkit-integration` | ExecutionKit integrator + deep-integration skill + repository recon + State continuity |
| `K00-019` or any explicit audit lifecycle action | `route.audit` | QA auditor; higher-priority independent audit route prevents self-acceptance |
| Native planning / architecture outside K00 | `route.planning` or fallback | product planner + domain architect + repository recon + traceability |
| Laravel/backend domain work | `route.backend` | backend engineer + Laravel-domain skill + data/execution rules |
| Flutter/offline/replay | `route.flutter` | Flutter operations + offline/state/café UX + offline contract |
| Bunova Cloud plans/subscriptions/entitlements | `route.platform` | platform operations + SaaS entitlement + platform contract |
| Menuza/payment/ETA/MikroTik/Restaurant interoperability | `route.integration` | integration engineer + integration/idempotency contracts |
| UI-bearing premium implementation | `route.ui` + premium supplement | café UX + premium construction + bilingual content |
| Content / public SEO | `route.content-seo` + SEO/content supplements | content depth + bounded public SEO policy |
| Security/release implementation | `route.release` | security/release + launch/test evidence |
| Product phase/deep audit | `route.audit` | QA auditor; implementation/review separation required |
| Review/revalidate/correct | `route.review` | QA + UX + security reviewers as applicable |

## Route precedence invariant

`route.audit` has priority 100 and `route.executionkit-integration` has priority 99. Therefore `K00-019`, whose projection declares `action: audit`, must resolve to the independent QA route; ordinary K00 tasks resolve to the dedicated integration specialist even when tags such as `premium`, `seo`, `state`, `agent` or `tooling` are also present. Supplements may add domain context but may not replace the primary lifecycle role.

## Representative dry-run cases required on host

The runtime-capable host must resolve at least: ordinary K00 work, `K00-019`, one backend task, one Flutter/offline task, one platform/SaaS task, one integration task, one premium UI task, a product phase audit and a blocker/review lifecycle action. Resolved context must include the expected specialist resources without turning fallback into an all-context dump.

All registered native resources must be reachable by a route, supplement, fallback or dependency expansion. Registration alone is not integration. Static manifest inspection is useful but does not replace `agent-doctor` and runtime context-resolution evidence.
