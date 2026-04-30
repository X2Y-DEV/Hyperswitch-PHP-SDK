# Hyperswitch PHP Client

[![CI](https://github.com/x2yio/hyperswitch-client-php/actions/workflows/ci.yml/badge.svg)](https://github.com/x2yio/hyperswitch-client-php/actions)
[![Packagist](https://img.shields.io/packagist/v/x2y/hyperswitch-client.svg)](https://packagist.org/packages/x2y/hyperswitch-client)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A pure-PHP SDK for the [Hyperswitch](https://hyperswitch.io) payment orchestration API.
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

use X2Y\HyperswitchClient\HyperswitchClient;
use X2Y\HyperswitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Enum\Currency;

$psr17 = new Psr17Factory();

$client = HyperswitchClient::sandbox(
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

## Maintenance guide

This SDK is a 1:1 mirror of the Hyperswitch HTTP API. It has zero framework
dependencies and could be lifted into any PHP project.

### Source of truth

`tests/Fixtures/postman.json` (the pinned Postman export). The Hyperswitch OpenAPI
spec is incorporated as a secondary source when available.

### Conventions

- Every Postman item → one Endpoint constant in `src/Endpoint/<Group>Endpoints.php`
- Every request body → one Request DTO in `src/Dto/<Group>/Request/`
- Every response shape → one Response DTO in `src/Dto/<Group>/Response/`
- Every closed-set field → an enum in `src/Enum/`
- File names are PascalCase mirroring Postman item names
- Every generated file carries `@postman`, `@endpoint`, `@hyperswitch-source` frontmatter

Full rules: [docs/conventions.md](docs/conventions.md).

### When Hyperswitch ships an API change

1. Replace `tests/Fixtures/postman.json` with the new export
   (`tools/update-postman.sh`).
2. Run: `composer test:drift`
3. The drift test will list missing endpoints / DTOs / enum cases.
4. Use the prompt in `tools/ai-maintenance-prompt.md` with an AI agent to fill
   the gaps.
5. Re-run `composer ci` until green.
6. Bump CHANGELOG.md with the new `@hyperswitch-source` date in modified files.
7. Tag a release.

### Architectural rules (do not relax)

- No `use Magento\…`, `Laravel\…`, `Symfony\…` (other than PSR interfaces) anywhere
  under `src/`. Enforced by phpstan rule and the drift test.
- DTOs are immutable. Constructor takes everything; no setters.
- DTOs implement `toArray()` (request) and `fromArray()` (response).
- Unknown response fields are preserved in `extras` to survive non-breaking
  Hyperswitch additions without crashing.
- One DTO per file. One enum per file.
- Resources contain no business logic — they only bind Endpoints to ApiClient.

## Architecture

See [docs/architecture.md](docs/architecture.md) for the layered design:
Resource → Endpoint → DTO → Transport → Auth.

## Testing

```bash
composer test:unit         # fast, no network
composer test:drift        # asserts Postman ↔ SDK coverage
composer test:integration  # requires HYPERSWITCH_SANDBOX_KEY
composer stan              # PHPStan at level max
composer ci                # everything CI runs
```

## Versioning

Semver. Minor versions track Hyperswitch API additions; major versions track
breaking changes. CHANGELOG.md records the Hyperswitch source date each release
was reconciled against.

## License

MIT. See [LICENSE](LICENSE).

## Maintained by

[X2Y](https://x2y.io). Built to power the X2Y Hyperswitch Magento module and
related integrations.
