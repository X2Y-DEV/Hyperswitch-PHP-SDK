# Conventions

Strict rules that AI maintenance and human contributors must follow. The drift
test and phpstan enforce most of them mechanically; a few are review-only.

The source of truth is the v1 OpenAPI spec (`tests/Fixtures/openapi_spec_v1.json`).
Map its constructs to SDK artifacts as follows:

| OpenAPI construct | SDK artifact |
|---|---|
| operation `tags[0]` | Group → `src/Endpoint/<Group>Endpoints.php`, `src/Dto/<Group>/`, `src/Resource/<Group>Resource.php` |
| `operationId` | `@openapi "<operationId>"`; PascalCase method / DTO base name |
| `METHOD` + path | `Endpoint::method` / `pathTemplate` |
| `security[]` scheme | `AuthTier` (see Auth rules) |
| `requestBody … schema.$ref` | Request DTO |
| `responses.2xx … schema.$ref` | Response DTO |
| component schema with `enum` | `src/Enum/<Name>.php` |
| `$ref`-shared schema (Address, Amount, …) | `src/Dto/Common/` |

## Naming

- **Spec tag → directory and Endpoint class.**
  `Payments` tag → `src/Endpoint/PaymentsEndpoints.php`,
  `src/Dto/Payment/{Request,Response}/`, `src/Resource/PaymentsResource.php`.
  Singular DTO directory, plural Endpoint and Resource files. Match the
  existing tree exactly when in doubt.
- **operationId → method / DTO name.** The v1 `operationId` is a human-readable
  phrase, not a slug — "Create a Payment" → `PaymentsEndpoints::create()`,
  `CreatePaymentRequest`, `PaymentResponse`. Strip stopwords ("a", "an", "the"),
  PascalCase the verb + noun.
- **PHP property naming: camelCase.** Map to/from the spec's snake_case keys in
  `toArray()` and `fromArray()`.
- **Enum cases: SCREAMING_SNAKE_CASE.** Backing values match the literal spec
  `enum` string (typically lowercase or snake_case).

## Per-file frontmatter

Every Endpoint factory, Resource method, Request DTO, and Response DTO must
carry this comment block:

```php
/**
 * @openapi             "<operationId>"    (source of truth; omit for shared DTOs and enums)
 * @endpoint            <METHOD> <path>    (omit for shared DTOs and enums)
 * @hyperswitch-source  <spec ref / YYYY-MM-DD>
 */
```

`@openapi` is the operation's `operationId`, the stable reference back into the
spec. `@hyperswitch-source` records the spec ref (or date) the file was last
reconciled against. Reviewers check these when evaluating drift.

## DTO rules

1. **One DTO per file.** Even if two operations share a payload — duplicate
   the DTO. Easier to evolve them separately later.
2. **Immutable.** All properties `public readonly`. Constructor takes every
   field. No setters. (Fluent `withFoo()` clone helpers are permitted on
   Request DTOs only, not Response DTOs.)
3. **Request DTOs:** extend `AbstractRequestDto`. Implement `toArray()` using
   `$this->filterNulls([...])` to drop optional `null` fields.
4. **Response DTOs:** extend `AbstractResponseDto`. Implement
   `public static function fromArray(array $data): static`. Pass
   `array_diff_key($data, array_flip($known))` as the `extras` argument so
   unknown fields are preserved.
5. **Use shared Common DTOs.** `Address`, `BillingDetails`, `Amount`,
   `PaginatedList`, etc. live in `src/Dto/Common/`. Don't duplicate them
   per group.
6. **Use existing enums for closed-set fields.** Search `src/Enum/` first.
   Add a new enum only when the field set isn't represented yet.

## Special-case fields

These conventions cover field shapes that HyperSwitch deliberately leaves
loose. Don't over-type them.

### Metadata fields

HyperSwitch lets merchants attach arbitrary key/value metadata to many
resources (`payments`, `refunds`, `customers`, `mandates`, …). The shape
is intentionally open.

- **Always type as `?array<string, mixed>`.**
- Property name: `$metadata`. JSON key: `metadata`.
- Don't introduce a wrapper class (`Metadata` value object). It would add
  one allocation per field for no type-safety gain over the typed array.
- Don't try to constrain the array's value type beyond `mixed` — HyperSwitch
  accepts strings, numbers, booleans, and nested objects.

### Connector-specific blob fields

When a request schema shows a connector-keyed nested object — such as
`split_refunds.stripe_split_refund`, `merchant_connector_details`,
`payment_method_data.card.connector_specific`, etc. — leave the field as
`?array<string, mixed>` rather than introducing a typed DTO.

- These shapes branch per connector (Stripe / Adyen / Worldpay / …) and
  HyperSwitch hasn't normalised them in the public API surface.
- A typed DTO would lock us into one connector's shape and break others.
- Add a docblock referencing the spec schema so reviewers know where
  the loose typing came from.

### Datetime fields

HyperSwitch is inconsistent across endpoints: most timestamps come back as ISO
8601 strings, but some endpoints — notably `POST /ephemeral_keys` — return
Unix timestamps as integers. **Always parse datetime fields via
`X2Y\HyperSwitchClient\Internal\DateTimeParser::parse()`.**

```php
createdAt: DateTimeParser::parse($data['created_at'] ?? null),
```

Do not call `new \DateTimeImmutable(...)` directly on response data — it will
throw on Unix-timestamp fields. The parser handles null, ISO 8601 strings,
integer Unix epochs, and numeric-string Unix epochs uniformly.

### Request DTO `extras` passthrough

HyperSwitch's larger bodies (Payments create / update / confirm in particular)
have 60+ fields, many connector-specific or rarely used. Modelling every field
with a typed accessor is high churn and provides little value for fields the
SDK author won't recognise.

**For Request DTOs with more than ~20 fields, accept an `extras` parameter:**

```php
public function __construct(
    public readonly int $amount,
    // ... typed fields ...
    public readonly array $extras = [],
) {}

public function toArray(): array
{
    $typed = $this->filterNulls([ /* … */ ]);
    return $this->mergeExtras($typed, $this->extras);
}
```

`mergeExtras()` is provided by `AbstractRequestDto`; typed fields win on key
collision so consumers can't accidentally mask a typed field via extras.

For small bodies (Capture, Cancel, IncrementalAuthorization), include `extras`
anyway for forward compatibility — the cost is one extra constructor argument.

### Multiple Request DTOs sharing one Endpoint

Sometimes the same `(method, path)` pair accepts multiple body shapes — e.g.
`POST /payments/{id}/confirm` accepts both a rich confirm body and a thin
confirm-with-token body. `POST /payments` itself is polymorphic across CIT,
MIT, setup-mandate, saved-card, and payment-link variants.

**One Endpoint factory, one Request DTO with all fields optional, one Resource
method.** Consumers populate the DTO differently per intent. Don't introduce
multiple Resource methods for the same Endpoint — it duplicates the dispatch
code without giving callers anything they couldn't get from a different
constructor invocation.

### List endpoints

Every list-style endpoint (paths ending `/list`, or with `list` semantics)
returns `PaginatedList<T>` from its Resource method.

- The Endpoint declares `responseClass: null` because the list envelope is
  decoded once by the Resource via `PaginatedList::fromArray`.
- The Resource constructs `PaginatedList<ItemDto>` by passing
  `ItemDto::fromArray(...)` as the item builder.
- Pagination metadata (`size`, `total_count`) is read from the envelope by
  `PaginatedList`; consumers access via `->size`, `->totalCount`, `->count()`,
  or `->items()`.

## Endpoint rules

1. Endpoint definitions live in `src/Endpoint/<Group>Endpoints.php` as a
   `final class` with a private constructor and one static factory method per
   spec operation.
2. Path templates use `{name}` placeholders, not `:name`.
3. `idempotent: true` only when HyperSwitch documents safe retries — generally
   `POST /payments`, `POST /refunds`, etc. Idempotency adds a header; don't
   set it on operations where retries change state.
4. `requestClass` is `null` when the endpoint takes no body (most GETs).
5. `responseClass` is `null` when the endpoint returns no body or returns a
   shape too dynamic to model — the ApiClient returns the raw decoded array
   in that case.

## Resource rules

1. Resources are thin facades. One public method per Endpoint. **No business
   logic.** No retries (middleware does that), no caching, no merging
   multi-call flows.
2. Resources only depend on `ApiClientInterface`, plus the Endpoints and DTOs
   for their group.
3. Path parameters and query parameters are explicit method arguments,
   not buried inside Request DTOs.

## Auth rules

1. Resources don't deal with auth — they declare an `AuthTier` on their
   Endpoint, and the ApiClient picks the right resolver. Map the operation's
   `security` scheme to a tier: `api_key`→`MERCHANT`, `publishable_key`→
   `PUBLISHABLE`, `admin_api_key`→`ADMIN`, `ephemeral_key`→`EPHEMERAL`,
   `jwt_key`→`JWT`. When an operation lists several, prefer the merchant-facing
   one (`api_key`, then `publishable_key`).
2. Don't introduce new auth tiers without updating the `AuthTier` enum, adding
   the corresponding resolver, and wiring it into the `ApiClient` resolver map.
   (The `JWT` tier for `jwt_key` operations is added the first time a group that
   needs it — Routing, Disputes, Events, Card Issuers — is modelled.)
3. **V1 endpoints (`/v1/payment-methods/*`, `/v1/payment-method-sessions/*`)
   use `AuthTier::MERCHANT_V1`** — same merchant key value as `MERCHANT`,
   but the wire format is `Authorization: api-key=<key>` rather than the
   standard `api-key: <key>` header. HyperSwitch's V1 surface also requires
   an `X-Profile-Id` header on every call; consumers attach it via
   `Resource::withProfileId('pro_xxx')` rather than per-call header args.

## Resource-scoped headers

For headers that need to be attached to every call from a Resource (the
`X-Profile-Id` header on V1 endpoints is the canonical example) the
consumer pattern is the fluent builder form:

```php
$client->paymentMethods()
       ->withProfileId('pro_42')
       ->retrieve('pm_xyz');
```

`AbstractResource::withExtraHeaders()` returns a *new* Resource instance
with the merged header set; the original is untouched, so the same base
Resource can serve multiple business contexts in the same process.
Per-call headers passed to the protected `execute()` helper override
Resource-scoped headers on key collision.

When adding a new Resource that needs scoped headers, prefer extending
`AbstractResource` (it handles `withExtraHeaders()` and `withProfileId()`
for free) over implementing `ResourceInterface` directly. The `execute()`
helper merges Resource-level extras automatically — call it instead of
`$this->client->execute()`.

## Architecture rules

1. **No framework imports under `src/`.** Specifically: no
   `use Magento\…`, `use Laravel\…`, `use Symfony\…` (other than `Psr\…`
   adapters from PSR packages, which belong in `psr/*` namespaces). Drift
   test + phpstan enforce this. Removing the rule requires removing the
   "framework-agnostic" claim from README.md.
2. **No code obfuscation, encrypted loaders, or anti-debugging tricks.** This
   is a transparent SDK; consumers should be able to read every line.
3. **No persistence.** No file writes, no database calls, no caching to disk.
   The SDK is stateless.
4. **No telemetry phone-home.** The SDK does not report usage, errors, or
   anything else to anyone outside the consumer's chosen logger.

## Forward compatibility

1. Response DTOs preserve unknown fields in `extras`. Don't drop fields silently.
2. Adding a new enum case is a minor version bump. Removing one is major.
3. Adding a new Endpoint or Resource method is a minor version bump.
4. Adding a new optional Request DTO field is a minor version bump.
5. Renaming or removing a Request DTO field is major.

## Tests

1. Every DTO ships with a round-trip test (`toArray()` ∘ `fromArray()` is the
   identity for known fields).
2. Every Endpoint factory ships with a smoke test asserting the method, path,
   and auth tier.
3. Resource tests use a mocked `ApiClientInterface`; do not boot real HTTP.
4. Integration tests live under `tests/Integration/` and require
   `HYPERSWITCH_SANDBOX_KEY`. Skip cleanly when the env var isn't set.

## When in doubt

Read [PaymentsResource](../src/Resource/PaymentsResource.php),
[CreatePaymentRequest](../src/Dto/Payment/Request/CreatePaymentRequest.php),
and [PaymentsEndpoints](../src/Endpoint/PaymentsEndpoints.php). Those are the
template. New code should look like them.
