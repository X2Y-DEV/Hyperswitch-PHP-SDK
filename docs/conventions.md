# Conventions

Strict rules that AI maintenance and human contributors must follow. The drift
test and phpstan enforce most of them mechanically; a few are review-only.

## Naming

- **Postman group → directory and Endpoint class.**
  `Payments` group → `src/Endpoint/PaymentsEndpoints.php`,
  `src/Dto/Payment/{Request,Response}/`, `src/Resource/PaymentsResource.php`.
  Singular DTO directory, plural Endpoint and Resource files. Match the
  existing tree exactly when in doubt.
- **Postman item → method / DTO name.** "Payments - Create" →
  `PaymentsEndpoints::create()`, `CreatePaymentRequest`, `PaymentResponse`.
  Drop hyphens, normalise spaces, PascalCase the verb.
- **PHP property naming: camelCase.** Map to/from snake_case keys in
  `toArray()` and `fromArray()`.
- **Enum cases: SCREAMING_SNAKE_CASE.** Backing values match the literal
  Hyperswitch string (typically lowercase or snake_case).

## Per-file frontmatter

Every Endpoint factory, Resource method, Request DTO, and Response DTO must
carry this comment block:

```php
/**
 * @postman             <Group> / <Item Name>
 * @endpoint            <METHOD> <path>     (omit for shared DTOs and enums)
 * @hyperswitch-source  YYYY-MM-DD
 */
```

`@hyperswitch-source` is the date the file was last reconciled against the
Postman export. Reviewers check this when evaluating drift.

## DTO rules

1. **One DTO per file.** Even if two Postman items share a payload — duplicate
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
5. **Use shared Common DTOs.** `Address`, `BillingDetails`, `Amount`, etc. live
   in `src/Dto/Common/`. Don't duplicate them per group.
6. **Use existing enums for closed-set fields.** Search `src/Enum/` first.
   Add a new enum only when the field set isn't represented yet.

## Endpoint rules

1. Endpoint definitions live in `src/Endpoint/<Group>Endpoints.php` as a
   `final class` with a private constructor and one static factory method per
   Postman item.
2. Path templates use `{name}` placeholders, not `:name`.
3. `idempotent: true` only when Hyperswitch documents safe retries — generally
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
   Endpoint, and the ApiClient picks the right resolver.
2. Don't introduce new auth tiers without updating `AuthTier` enum and
   adding the corresponding resolver implementation.

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
