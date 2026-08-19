# HyperSwitch PHP Client

[![CI](https://github.com/X2Y-DEV/hyperswitch-client-php/actions/workflows/ci.yml/badge.svg)](https://github.com/X2Y-DEV/hyperswitch-client-php/actions)
[![Packagist](https://img.shields.io/packagist/v/x2y/hyperswitch-client.svg)](https://packagist.org/packages/x2y/hyperswitch-client)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A pure-PHP SDK for the [HyperSwitch](https://hyperswitch.io) payment orchestration API.
Framework-agnostic, PSR-18 / PSR-3 compatible, built for full coverage of the public
API surface.

## Installation

```bash
composer require x2y/hyperswitch-client
```

Requires PHP 8.1+ and a PSR-18 HTTP client (e.g. `symfony/http-client`,
`guzzlehttp/guzzle`).

## Quick start

```php
use Symfony\Component\HttpClient\Psr18Client;
use Nyholm\Psr7\Factory\Psr17Factory;

use X2Y\HyperSwitchClient\HyperSwitchClient;
use X2Y\HyperSwitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperSwitchClient\Enum\Currency;

$psr17 = new Psr17Factory();

$client = HyperSwitchClient::sandbox(
    httpClient:     new Psr18Client(),
    requestFactory: $psr17,
    streamFactory:  $psr17,
    auth:           new MerchantKeyAuthResolver('snd_…'),
);

$response = $client->payments()->create(new CreatePaymentRequest(
    amount:   4500,
    currency: Currency::USD,
));

echo $response->paymentId;     // pay_abc123
echo $response->clientSecret;  // pay_…_secret_…
```

## Receiving webhooks

HyperSwitch signs outbound webhook bodies with HMAC (SHA-512 by default) using
the merchant's "Payment Response Hash Key". The SDK ships a verifier and a
typed event decoder — verification happens before JSON decode, so an invalid
signature short-circuits without exposing the body to the JSON parser.

```php
use X2Y\HyperSwitchClient\Enum\EventType;
use X2Y\HyperSwitchClient\Exception\InvalidSignatureException;
use X2Y\HyperSwitchClient\Exception\InvalidWebhookPayloadException;
use X2Y\HyperSwitchClient\Webhook\EventDecoder;
use X2Y\HyperSwitchClient\Webhook\SignatureVerifier;

$decoder = new EventDecoder(new SignatureVerifier(getenv('HYPERSWITCH_WEBHOOK_KEY')));

$body      = (string) $request->getBody();
$signature = $request->getHeaderLine('X-Webhook-Signature-512');

try {
    $event = $decoder->decode($body, $signature);
} catch (InvalidSignatureException) {
    return $response->withStatus(401);
} catch (InvalidWebhookPayloadException) {
    return $response->withStatus(400);
}

match ($event->eventType) {
    EventType::PAYMENT_SUCCEEDED      => $this->markOrderPaid($event->asPayment()),
    EventType::REFUND_SUCCEEDED       => $this->creditMemoFor($event->asRefund()),
    EventType::DISPUTE_OPENED         => $this->flagDispute($event->asDispute()),
    EventType::SUBSCRIPTION_RENEWAL_SUCCEEDED
                                      => $this->createOrderFrom($event->asSubscription()),
    null                              => $this->logUnknown($event->eventTypeRaw),
    default                           => $this->logUnhandled($event),
};
```

`$event->eventType` is `null` when HyperSwitch ships an event type not yet
enumerated by the SDK — the raw string is always available on
`$event->eventTypeRaw`. See [docs/examples/webhook-receiver.php](docs/examples/webhook-receiver.php)
for a runnable bare-PHP example.

## API coverage

The SDK models **all 150 operations** of the HyperSwitch **v1 OpenAPI spec**
(`OpenApiCoverageTest` enforces 1:1 coverage in both directions). Resource accessors:

| Group | Resource accessor |
|---|---|
| Payments | `$client->payments()` |
| Refunds | `$client->refunds()` |
| Customers | `$client->customers()` |
| Mandates | `$client->mandates()` |
| Subscriptions | `$client->subscriptions()` |
| Payment Methods (v1) | `$client->paymentMethodsV1()` |
| Payment Methods (v2 compat) | `$client->paymentMethods()` |
| PaymentMethodSessions (v2 compat) | `$client->paymentMethodSessions()` |
| Disputes | `$client->disputes()` |
| Payouts | `$client->payouts()` |
| PaymentLinks | `$client->paymentLinks()` |
| ApiKeys | `$client->apiKeys()` |
| BusinessProfile | `$client->businessProfile()` |
| MerchantAccounts | `$client->merchantAccounts()` |
| Connectors | `$client->connectors()` |
| Platform / Organization | `$client->platform()` |
| Routing | `$client->routing()` |
| Authentication (3DS) | `$client->authentication()` |
| Events (webhook delivery) | `$client->events()` |
| Blocklist | `$client->blocklist()` |
| GSM | `$client->gsm()` |
| Card Issuers | `$client->cardIssuer()` |
| Profile Acquirers | `$client->profileAcquirer()` |
| Poll | `$client->poll()` |
| 3DS Decision Rule | `$client->threeDsDecision()` |
| Relay | `$client->relay()` |
| Proxy (v2 compat) | `$client->proxy()` |
| **Webhooks** (inbound) | `Webhook\EventDecoder` |

The `/v1/payment-methods*`, `/v1/payment-method-sessions*` and `/v1/proxy` paths
are the **v2** modular-payments surface (present in `openapi_spec_v2`, not v1);
they are kept as documented `SDK_ONLY_ALLOWED` exceptions and belong to a future,
separate v2 SDK. See [docs/v1-alignment.md](docs/v1-alignment.md).

Auth tiers: `MERCHANT` (api-key), `PUBLISHABLE`, `ADMIN`, `EPHEMERAL`,
`MERCHANT_V1` (api-key=… + X-Profile-Id), and `JWT` (Bearer, for event management).

V1 endpoints (PaymentMethods, PaymentMethodSessions) require an
`X-Profile-Id` header; consumers attach it via the fluent builder:

```php
$client->paymentMethods()
       ->withProfileId('pro_42')
       ->retrieve('pm_xyz');
```

## Maintenance guide

This SDK is a 1:1 mirror of the HyperSwitch HTTP API. It has zero framework
dependencies and could be lifted into any PHP project.

### Source of truth

`tests/Fixtures/openapi_spec_v1.json` — the first-party HyperSwitch **v1 OpenAPI
spec**, pinned to a hyperswitch commit by `tools/update-openapi.sh`. Drift
detection (`OpenApiCoverageTest`) and AI maintenance both pivot on it: typed
schemas, declared enums, and per-operation `security` make the maintenance loop
mechanical. (The SDK models all 150 v1 operations; the legacy Postman export and
its drift test have been removed now that the OpenAPI test is authoritative.)

### Conventions

- Every spec operation → one Endpoint factory in `src/Endpoint/<Group>Endpoints.php`
- Every request body schema → one Request DTO in `src/Dto/<Group>/Request/`
- Every response schema → one Response DTO in `src/Dto/<Group>/Response/`
- Every schema `enum` → an enum in `src/Enum/`
- The group comes from the operation's `tags[0]`; the method/DTO name from its `operationId`
- Every generated file carries `@openapi`, `@endpoint`, `@hyperswitch-source` frontmatter

Full rules: [docs/conventions.md](docs/conventions.md).

### When HyperSwitch ships an API change

1. Re-pin the spec (`tools/update-openapi.sh`, bumping `HYPERSWITCH_REF`).
2. Run: `composer test:drift`
3. The drift test lists spec operations missing from the SDK (and any SDK-only drift).
4. Use the prompt in `tools/ai-maintenance-prompt.md` with an AI agent to fill the
   gaps and remove the closed keys from `OpenApiCoverageTest::EXCLUDED`.
5. Re-run `composer ci` until green.
6. Bump CHANGELOG.md with the new spec ref in modified files' `@hyperswitch-source`.
7. Tag a release.

### Architectural rules (do not relax)

- No `use Magento\…`, `Laravel\…`, `Symfony\…` (other than PSR interfaces) anywhere
  under `src/`. Enforced by phpstan rule and the drift test.
- DTOs are immutable. Constructor takes everything; no setters.
- DTOs implement `toArray()` (request) and `fromArray()` (response).
- Unknown response fields are preserved in `extras` to survive non-breaking
  HyperSwitch additions without crashing.
- One DTO per file. One enum per file.
- Resources contain no business logic — they only bind Endpoints to ApiClient.

## Architecture

See [docs/architecture.md](docs/architecture.md) for the layered design:
Resource → Endpoint → DTO → Transport → Auth.

## Testing

```bash
composer test:unit         # fast, no network
composer test:drift        # asserts v1 OpenAPI spec ↔ SDK coverage
composer test:integration  # requires HYPERSWITCH_SANDBOX_KEY
composer stan              # PHPStan at level max
composer ci                # unit + drift + stan (no network; integration excluded)
```

### Integration tests against the sandbox

The integration suite hits the real HyperSwitch **sandbox** and is gated behind
environment variables. Each test skips cleanly when its variable is unset, so
`composer ci` and contributors without sandbox access are unaffected.

| Variable | Required | Purpose |
|---|---|---|
| `HYPERSWITCH_SANDBOX_KEY` | yes | Merchant `snd_…` key; without it the whole suite skips. |
| `HYPERSWITCH_SANDBOX_PROFILE_ID` | no | A `pro_…` id to exercise profile-scoped V1 paths. |
| `HYPERSWITCH_WEBHOOK_KEY` | no | Payment Response Hash Key; enables the webhook test. |

```bash
HYPERSWITCH_SANDBOX_KEY='snd_yourKeyHere' composer test:integration
```

With the optional variables, and running a single file directly:

```bash
HYPERSWITCH_SANDBOX_KEY='snd_…' \
HYPERSWITCH_SANDBOX_PROFILE_ID='pro_…' \
HYPERSWITCH_WEBHOOK_KEY='whsec_…' \
vendor/bin/phpunit tests/Integration/CustomersIntegrationTest.php
```

Tests create real sandbox resources (prefixed `sdkit_…`) and remove them in a
best-effort `tearDown`; cleanup failures are logged to STDERR but do not fail
the run. Prefer `export`-ing the key for your session rather than inlining it,
to keep credentials out of your shell history.

## Versioning

Semver. Minor versions track HyperSwitch API additions; major versions track
breaking changes. CHANGELOG.md records the HyperSwitch source date each release
was reconciled against.

## License

MIT. See [LICENSE](LICENSE).

## Maintained by

[X2Y.DEV](https://x2y.dev). Built to power the X2Y HyperSwitch Magento
module and related integrations.
