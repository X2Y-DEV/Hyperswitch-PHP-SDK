# Changelog

All notable changes to `x2y/hyperswitch-client` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Each release records the `@hyperswitch-source` ref the SDK was reconciled
against the upstream HyperSwitch OpenAPI spec.

## [Unreleased]

## [0.2.0] — 2026-11-23

### Changed

- **Single mechanical source of truth is now the first-party HyperSwitch v1
  OpenAPI spec** (`tests/Fixtures/openapi_spec_v1.json`, pinned to hyperswitch
  `ada4a1e`), fetched by `tools/update-openapi.sh`. `OpenApiCoverageTest` enforces
  1:1 coverage against it in both directions.
- `BillingDetails` now nests the contact number as a `PhoneDetails`
  `{number, country_code}` object; payout update is `POST /payouts/{id}` (was PUT);
  `convert_to_platform` path corrected to `/organization/{id}/convert_to_platform`
  (verified against the sandbox).
- AI-maintenance prompt and docs pivot on the OpenAPI spec (`@openapi`
  frontmatter, tag→group / operationId→method mapping).

### Added

- **Full v1 coverage — all 150 `openapi_spec_v1.json` operations are modelled.**
  New groups: Routing, Authentication (3DS), Events, Blocklist, GSM, Card Issuers,
  Profile Acquirers, Poll, 3DS Decision Rule, and the v1 Payment Methods surface
  (`paymentMethodsV1()`); plus additions across Payments, Payouts, Disputes,
  Customers, Organization, MerchantAccounts, Connectors and Subscriptions.
- `AuthTier::JWT` + `JwtKeyAuthResolver` (`Authorization: Bearer <token>`) for
  event-management endpoints.

### Source

- `@hyperswitch-source: ada4a1e` — hyperswitch's `api-reference/v1/openapi_spec_v1.json` at that commit.

## [0.1.0] — 2026-04-30

### Added

- Initial coverage of the HyperSwitch API: 17 resource groups, ~83 endpoints.
- Resource facades:
  - **Payments** (11 endpoints) — create / update / retrieve / confirm /
    capture / cancel / cancelPostCapture / list / sessionTokens /
    incrementalAuthorization / extendAuthorization. Polymorphic
    `POST /payments` covers CIT, MIT, setup-mandate, saved-card and
    payment-link variants.
  - **Refunds** (4) — create / update / retrieve / list.
  - **Customers** (6) — create / retrieve / update / delete / list /
    ephemeralKey.
  - **Mandates** (3 unique) — retrieve / listForCustomer / revoke.
  - **Subscriptions** (11) — full lifecycle including create / confirm /
    pause / resume / cancel / update / list / plans / estimate.
  - **PaymentMethods V1** (6) — create / retrieve / update / delete /
    listForCustomer / tokenDetails.
  - **PaymentMethodSessions** (6) — create / retrieve /
    listPaymentMethods / updateSavedPaymentMethod / confirm / tokenLookup.
  - **Disputes** (4) — retrieve / list / accept / submitEvidence.
  - **Payouts** (6) — create / update / retrieve / confirm / fulfill / cancel.
  - **PaymentLinks** (1) — retrieve. (Create reuses POST /payments.)
  - **ApiKeys** (5) — admin-tier API key lifecycle.
  - **BusinessProfile** (5) — admin-tier profile management.
  - **MerchantAccounts** (4) — admin-tier merchant onboarding.
  - **Connectors** (5) — admin-tier upstream PSP attachment.
  - **Platform** (3) — admin-tier organization / platform conversion.
  - **Relay** (2) — cross-connector capture/void/refund/incremental-auth.
  - **Proxy** (1) — vault-token proxy.
- **Webhook handling**: `Webhook\SignatureVerifier` (HMAC-SHA512 / SHA-256,
  constant-time), `Webhook\EventDecoder`, `Webhook\WebhookEvent` envelope
  with typed `asPayment()` / `asRefund()` / `asDispute()` / `asMandate()` /
  `asPayout()` / `asSubscription()` / `asCustomer()` extractors. Forward-
  compatible against new HyperSwitch event types via `tryFrom`-based enum
  lookup with raw fallback on `WebhookEvent::$eventTypeRaw`.
- **Auth tiers**: ADMIN, MERCHANT, MERCHANT_V1, PUBLISHABLE, EPHEMERAL.
  V1 endpoints (PaymentMethods, PaymentMethodSessions) authenticate via
  `Authorization: api-key=<key>` rather than the standard `api-key:` header
  and require `X-Profile-Id` (attached via `Resource::withProfileId()`).
- **Resource-scoped headers**: `AbstractResource::withExtraHeaders()` and
  `withProfileId()` fluent builders. Stripe-Connect-style pattern:
  `$client->subscriptions()->withProfileId('pro_42')->retrieve($id)`.
- **DTO infrastructure**: immutable typed DTOs across all resources;
  `extras` passthrough on Response DTOs preserves unknown fields;
  `mergeExtras()` on Request DTOs lets consumers send fields the SDK
  hasn't typed yet.
- **PaginatedList<T>**: generic envelope handling both `{data: [...]}`
  and bare-array list shapes that HyperSwitch returns inconsistently.
- **Internal helpers**: `Json::decode` / `Json::decodeAny` (object-only vs
  any-shape), `DateTimeParser::parse` (ISO 8601 + Unix epoch int + numeric
  string).
- **Transport**: PSR-18-based `ApiClient` with onion-style middleware
  (idempotency-key UUIDv4, retry with backoff, PSR-3 logging,
  error-mapping), full status-to-typed-exception mapping (Authentication
  / Validation / NotFound / RateLimited / Transient / Transport /
  generic Api), three error-shape extractors covering nested `error.message`,
  flat `error_message`, IR_xx-style `{type, code, message}`, and plain-text
  fallback.
- **Drift test** (`composer test:drift`) — asserts every (METHOD, path) in the
  HyperSwitch API surface is covered by an Endpoint factory in `src/Endpoint/`.
  CI gate.
- **Test suites**:
  - 120 unit tests covering Transport (ApiClient, Endpoint, PathBinder,
    middleware), Auth (4 resolvers), Internal (Json, DateTimeParser),
    DTO base classes (filterNulls, mergeExtras, extras passthrough),
    Common DTOs (Address, BillingDetails, PaginatedList), Webhooks
    (signature pass/fail, decoder, EventType lookup), and hot-path
    Response DTOs (round-trip from real fixtures).
  - drift tests (fixture readability, no-framework-imports rule,
    full-coverage gate).
  - 15 sandbox integration tests behind `HYPERSWITCH_SANDBOX_KEY` and
    optional `HYPERSWITCH_WEBHOOK_KEY` env vars.
- **CI** (GitHub Actions): unit + drift + PHPStan level 8 across PHP
  8.1 / 8.2 / 8.3 / 8.4. Coverage (PCOV) on PHP 8.4 only; integration
  tests on a nightly schedule.
- **Documentation**: README with quick-start + webhook reception +
  coverage table; `docs/architecture.md` (layered design); `docs/conventions.md`
  (mechanical rules for AI-driven maintenance); `tools/ai-maintenance-prompt.md`
  (the canonical prompt).

### Source

- `@hyperswitch-source: 2026-04-30` — the HyperSwitch API surface as of that date.

### Notes

- Pre-1.0 — public API may shift before 1.0.0 based on consumer
  feedback from the X2Y HyperSwitch Magento module.
