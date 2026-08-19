# AI maintenance prompt

This is the canonical prompt used when an AI agent updates the SDK against the
pinned HyperSwitch **v1 OpenAPI spec**. Paste it verbatim along with the output
of `composer test:drift`.

The single mechanical source of truth is `tests/Fixtures/openapi_spec_v1.json`
(first-party, versioned, pinned to a hyperswitch commit by
`tools/update-openapi.sh`). The v2 API (`openapi_spec_v2.json`) is a **separate,
future SDK** and is out of scope here.

---

You are maintaining the `x2y/hyperswitch-client` PHP SDK. The single source of
truth is `tests/Fixtures/openapi_spec_v1.json`. Read `docs/conventions.md` and
`docs/architecture.md` before generating any code. Follow every rule strictly.

## Your task

`composer test:drift` (the `OpenApiCoverageTest`) reports two kinds of drift:

1. **Coverage gaps** — spec operations not yet in the SDK. They are enumerated in
   `OpenApiCoverageTest::EXCLUDED`, tagged by priority/group (`gap-p1-payments`,
   `gap-p2-routing`, …). Model the operations for the group you are asked to
   close, then **remove those keys from `EXCLUDED`** so the test enforces the new
   coverage.
2. **SDK-only endpoints** — SDK endpoints absent from the spec. These are drift;
   either fix the endpoint to match the spec or, if it is a genuine documented
   exception, add it to `OpenApiCoverageTest::SDK_ONLY_ALLOWED` with a reason.

Generate only the files needed for the requested group. Do not modify files
outside that scope. Do not change conventions. Do not add new dependencies.

## Reading a spec operation

For a gap like `POST /payments/{id}/complete_authorize`, open
`tests/Fixtures/openapi_spec_v1.json` at `paths["/payments/{payment_id}/complete_authorize"].post`
and read:

- `operationId` — a human-readable phrase (e.g. `"Complete Authorize a Payment"`).
  Use it as the `@openapi` reference tag; derive the PHP method/DTO name from it
  in PascalCase (strip stopwords: "Complete Authorize a Payment" → `completeAuthorize`).
- `tags[0]` — the group (e.g. `Payments`, `Routing`, `Disputes`). Maps to
  `src/Endpoint/<Group>Endpoints.php`, `src/Dto/<Group>/…`, `src/Resource/<Group>Resource.php`.
- `security[]` — the auth scheme(s); map to an `AuthTier` (table below).
- `requestBody.content["application/json"].schema.$ref` — the Request DTO source.
  Follow the `$ref` into `components.schemas.<Name>`; each property → a typed
  `public readonly` field; `enum` properties → an `src/Enum/` enum; nested
  `$ref` objects → a Common DTO or a nested Request DTO.
- `responses["200"|"2xx"].content["application/json"].schema.$ref` — the Response
  DTO source, same walk.
- Path/query `parameters[]` — explicit Resource method arguments.

## Mapping: OpenAPI → SDK

| OpenAPI construct | SDK artifact |
|---|---|
| `tags[0]` | Group → `<Group>Endpoints.php`, `Dto/<Group>/`, `<Group>Resource.php` |
| `operationId` | `@openapi "<operationId>"`; PascalCase method / DTO base name |
| `METHOD` + path | `Endpoint::method` / `pathTemplate` (`@endpoint METHOD /path`) |
| `security[]` → scheme | `AuthTier` (see below) |
| `requestBody … schema.$ref` | Request DTO |
| `responses.2xx … schema.$ref` | Response DTO |
| component schema with `enum` | `src/Enum/<Name>.php` |
| `$ref`-shared schema (Address, Amount, …) | `src/Dto/Common/` |
| open shapes (`metadata`, connector blobs) | `?array<string, mixed>` |

### security scheme → AuthTier

| spec scheme | AuthTier |
|---|---|
| `api_key` | `AuthTier::MERCHANT` |
| `publishable_key` | `AuthTier::PUBLISHABLE` |
| `admin_api_key` | `AuthTier::ADMIN` |
| `ephemeral_key` | `AuthTier::EPHEMERAL` |
| `jwt_key` | `AuthTier::JWT` |

If an operation lists multiple schemes, pick the primary merchant-facing one
(prefer `api_key`, then `publishable_key`). `/v1/…`-prefixed paths use
`AuthTier::MERCHANT_V1` + `X-Profile-Id` (these are the v2-compat surface).

## Conventions you must follow

1. **One artifact per file.** One DTO per file, one enum per file, one
   `<Group>Endpoints.php` per group.
2. **Frontmatter required on every generated file:**
   ```php
   /**
    * @openapi             "<operationId>"     (source of truth; omit for enums/shared DTOs)
    * @endpoint            <METHOD> <path>     (omit for enums and shared DTOs)
    * @hyperswitch-source  <spec ref / YYYY-MM-DD>
    */
   ```
3. **DTOs:**
   - Request DTOs extend `AbstractRequestDto`. All properties `public readonly`.
     Implement `toArray()` returning `$this->filterNulls([...])` (snake_case keys).
   - Response DTOs extend `AbstractResponseDto`. Implement
     `public static function fromArray(array $data): static`, passing unrecognised
     fields as `extras` (`array_diff_key($data, array_flip($known))`).
   - PHP camelCase properties; map to/from the spec's snake_case keys.
4. **Enums:** PHP 8.1 backed string enums implementing `LabelledEnum`. Cases match
   the spec's literal `enum` values exactly; `label()` returns a short description.
5. **Resources:** thin facades, one method per Endpoint, no business logic; depend
   only on `ApiClientInterface`; path/query params are explicit method args.
6. **No framework imports.** No `use Magento\…`, `use Laravel\…`, `use Symfony\…`.
   Only `Psr\…` interfaces are permitted from outside the SDK namespace. The drift
   test enforces this with a regex check.
7. **Preserve forward compatibility.** Response DTOs always pass unknown fields into
   `extras`. Never drop unknown fields silently.
8. **Reuse existing enums and Common DTOs.** Don't create `Currency2` or duplicate
   `Address`. Search `src/Enum/` and `src/Dto/Common/` first.
9. **Special-case fields — see `docs/conventions.md`:**
   - `metadata` is always `?array<string, mixed>`. Don't wrap.
   - Connector-specific blob fields stay `?array<string, mixed>`.
   - List endpoints return `PaginatedList<T>` from the Resource (Endpoint declares
     `responseClass: null`).
   - Datetime fields use `Internal\DateTimeParser::parse($data['x'] ?? null)` — never
     construct `\DateTimeImmutable` directly (some endpoints return Unix epochs).
   - Request DTOs with >20 fields take a final `array $extras = []` and merge via
     `$this->mergeExtras($typed, $this->extras)`.
   - When several operations share a (method, path) pair, register ONE Endpoint
     factory and ONE Resource method with a single all-optional Request DTO.
10. **New AuthTier `JWT`:** if the group needs `jwt_key` and `AuthTier::JWT` does not
    yet exist, add the enum case and a resolver in `src/Auth/`, and wire it into the
    `ApiClient` resolver map — following the existing resolver pattern exactly.

## Output format

For each missing file, generate the full file contents. Do not abbreviate. Then
update `OpenApiCoverageTest::EXCLUDED` (remove the closed keys). After generating,
list any: operations you skipped and why; fields whose types you couldn't infer
(and your default); new enums introduced.

## Verification

```bash
composer ci
```

All checks must pass: `composer test:unit`, `composer test:drift` (the group's
`EXCLUDED` keys removed, zero unexpected drift), `composer stan` (phpstan max). Do
not push changes that fail CI.

## What you must not do

- Don't introduce new dependencies in `composer.json`.
- Don't change the architecture or layering.
- Don't add code obfuscation, encryption, or anti-debugging tricks.
- Don't log API keys, customer PII, or response bodies in middleware.
- Don't merge multiple DTOs into one file.
- Don't relax the framework-import ban.
- Don't delete an `EXCLUDED`/`SDK_ONLY_ALLOWED` key to silence a failure without
  actually modeling or reconciling the endpoint.
- Don't change the `@hyperswitch-source` value on files you didn't modify.
