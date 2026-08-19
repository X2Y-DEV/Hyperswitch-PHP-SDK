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

| Group | Endpoints | Resource accessor |
|---|---|---|
| Payments | 11 | `$client->payments()` |
| Refunds | 4 | `$client->refunds()` |
| Customers | 6 | `$client->customers()` |
| Mandates | 3 | `$client->mandates()` |
| Subscriptions | 11 | `$client->subscriptions()` |
| PaymentMethods (V1) | 6 | `$client->paymentMethods()` |
| PaymentMethodSessions | 6 | `$client->paymentMethodSessions()` |
| Disputes | 4 | `$client->disputes()` |
| Payouts | 6 | `$client->payouts()` |
| PaymentLinks | 1 | `$client->paymentLinks()` |
| ApiKeys | 5 | `$client->apiKeys()` |
| BusinessProfile | 5 | `$client->businessProfile()` |
| MerchantAccounts | 4 | `$client->merchantAccounts()` |
| Connectors | 5 | `$client->connectors()` |
| Platform | 3 | `$client->platform()` |
| Relay | 2 | `$client->relay()` |
| Proxy | 1 | `$client->proxy()` |
| **Webhooks** (inbound) | n/a | `Webhook\EventDecoder` |

The SDK is being realigned to the HyperSwitch **v1 OpenAPI spec** (150 operations).
Currently ~65 are modelled; the remaining gaps are enumerated and categorized in
`OpenApiCoverageTest::EXCLUDED` and are being closed group-by-group (see
[docs/v1-alignment.md](docs/v1-alignment.md)). The `/v1/payment-methods*` and
`/v1/payment-method-sessions*` paths are the **v2** modular-payments surface and
belong to a future, separate v2 SDK.

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
mechanical. (The Postman export lingers only as a transitional cross-check and
will be removed once the v1 burndown completes.)

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
