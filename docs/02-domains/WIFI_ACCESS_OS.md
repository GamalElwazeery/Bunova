# Wi-Fi Access OS

## Goal

Treat guest Wi-Fi as a commercial/operational entitlement integrated with café billing, not as an unrelated router utility.

## Adapter architecture

The Wi-Fi domain defines normalized router operations. MikroTik is the first adapter; future router vendors implement the same contract.

Normalized operations include health/connectivity check, create/update/revoke credential or entitlement, query usage/session where supported, disconnect session, and reconcile router state.

## Package dimensions

A package may limit:

- validity window;
- active-use duration;
- data quota;
- upload/download speed;
- concurrent devices;
- branch/router/SSID applicability;
- schedule/daypart;
- price or free entitlement condition.

## Voucher/entitlement lifecycle

`created/issued -> activated -> active -> exhausted/expired/revoked` with `provisioning_failed` and `reconciliation_required` operational states.

Credentials may be generated code/password/QR token depending on router/captive portal design. Secrets should not be unnecessarily retained in plaintext.

## Sales integration

Selling a package creates a canonical billable line. Free voucher triggered by spend/loyalty is still represented as an entitlement with source/reason and zero/discounted financial treatment.

## Captive portal

Portal can display Bunova/Menuza branding, voucher entry, terms/consent and links to digital menu/order. Menuza remains owner of customer menu experience; captive portal only links/embeds through an explicit integration pattern.

## Failure behavior

Router offline must not create fake active access. Paid voucher provisioning failures are visible, retryable and reconciled/refundable according to policy. Router state drift is periodically reconciled.

## Security

Router credentials are encrypted secrets, never exposed to ordinary staff. Adapter calls are least-privileged, rate-limited, logged and isolated from public web traffic.
