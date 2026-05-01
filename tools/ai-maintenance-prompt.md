# AI maintenance prompt

This is the canonical prompt used when an AI agent updates the SDK against
a new Postman export. Paste it verbatim along with the output of
`composer test:drift`.

Note: HyperSwitch does not publish a public OpenAPI specification, so the
Postman collection at `tests/Fixtures/postman.json` is the only mechanical
source of truth.

---

You are maintaining the `x2y/hyperswitch-client` PHP SDK. The single source of
truth is `tests/Fixtures/postman.json`. Read `docs/conventions.md` and
`docs/architecture.md` before generating any code. Follow every rule strictly.

## Your task

The drift test output below lists endpoints, DTOs, or enums that exist in the
Postman fixture but not in the SDK. Generate the missing files. Do not modify
files outside the listed gaps. Do not change conventions. Do not add new
dependencies.

## Conventions you must follow

1. **One artifact per file.** One DTO per file, one enum per file, one
   `<Group>Endpoints.php` per Postman group.
2. **PascalCase class names** mirroring Postman item names. Strip punctuation
   ("Payments - Create" → `CreatePayment`, then suffix `Request` / `Response`).
3. **Predictable file paths:**
   - Endpoint definitions: `src/Endpoint/<Group>Endpoints.php`
   - Request DTOs: `src/Dto/<Group>/Request/<Action>Request.php`
   - Response DTOs: `src/Dto/<Group>/Response/<Action>Response.php`
   - Enums: `src/Enum/<Name>.php`
   - Resources: `src/Resource/<Group>Resource.php`
4. **Frontmatter required on every generated file:**
   ```php
   /**
    * @postman             <Group> / <Item Name>
    * @endpoint            <METHOD> <path>      (omit for enums and shared DTOs)
    * @hyperswitch-source  <YYYY-MM-DD>         (today's date)
    */
   ```
5. **DTOs:**
   - Request DTOs extend `AbstractRequestDto`. All properties `public readonly`.
     Constructor accepts every field. Implement `toArray()` returning
     `$this->filterNulls([...])`.
   - Response DTOs extend `AbstractResponseDto`. Same readonly pattern.
     Implement `public static function fromArray(array $data): static` that
     builds the DTO and passes unrecognised fields to `parent::__construct($extras)`.
   - Field naming: PHP camelCase properties; map to/from snake_case keys in
     `toArray()` / `fromArray()`.
6. **Enums:** PHP 8.1 backed string enums implementing `LabelledEnum`.
   Cases match HyperSwitch's literal string values. `label()` returns a short
   English description.
7. **Resources:** Extend `ResourceInterface`. One method per Endpoint. No
   business logic — bind Endpoint, dispatch via injected `ApiClientInterface`,
   cast response, return.
8. **No framework imports.** No `use Magento\…`, `use Laravel\…`,
   `use Symfony\…`. Only `Psr\…` interfaces are permitted from outside the
   SDK's own namespace. The drift test enforces this with a regex check.
9. **Preserve forward compatibility.** Response DTOs always pass unknown fields
   into `extras`. Don't drop unknown fields silently.
10. **Use existing enums and Common DTOs where they apply.** Don't create
    `Currency2` or duplicate `Address`. Search `src/Enum/` and
    `src/Dto/Common/` first.
11. **Special-case fields — see `docs/conventions.md`:**
    - `metadata` fields are always `?array<string, mixed>`. Don't wrap.
    - Connector-specific blob fields (`split_refunds.<connector>`,
      `merchant_connector_details`, etc.) stay `?array<string, mixed>`.
    - List endpoints return `PaginatedList<T>` from the Resource (Endpoint
      declares `responseClass: null` so the Resource can wrap manually).
    - Datetime fields use `Internal\DateTimeParser::parse($data['x'] ?? null)`.
      Never construct `\DateTimeImmutable` directly on response data —
      HyperSwitch returns Unix epoch ints on some endpoints (e.g.
      `/ephemeral_keys`) and ISO 8601 elsewhere.
    - Request DTOs with >20 fields take a final `array $extras = []`
      parameter and merge via `$this->mergeExtras($typed, $this->extras)`
      so consumers can pass fields the SDK has not typed yet.
    - When several Postman items share a (method, path) pair (e.g. the
      polymorphic `POST /payments` create and `POST /payments/{id}/confirm`),
      register ONE Endpoint factory and ONE Resource method. Use a single
      Request DTO with optional fields covering every variant.
    - V1 paths (`/v1/payment-methods/*`, `/v1/payment-method-sessions/*`)
      declare `AuthTier::MERCHANT_V1` and require `X-Profile-Id`. Resources
      targeting V1 endpoints extend `AbstractResource` so consumers can
      attach the profile id via `withProfileId('pro_xxx')`.

## Output format

For each missing file, generate the full file contents. Do not abbreviate.
After generating all files, list any:
- Postman items you skipped and why (e.g. duplicate of another endpoint)
- Fields you couldn't infer types for (and your default choice)
- New enums you introduced (so they can be reviewed)

## Verification

After generating, run:

```bash
composer ci
```

All checks must pass:
- `composer test:unit` — unit tests
- `composer test:drift` — drift test, must show zero missing endpoints
- `composer stan` — phpstan at level max

If any check fails, report the failure and the file(s) involved. Do not push
changes that fail CI.

## What you must not do

- Don't introduce new dependencies in `composer.json`.
- Don't change the architecture or layering.
- Don't add code obfuscation, encryption, or anti-debugging tricks.
- Don't log API keys, customer PII, or response bodies in middleware.
- Don't merge multiple DTOs into one file.
- Don't relax the framework-import ban.
- Don't change the `@hyperswitch-source` date on files you didn't modify.
