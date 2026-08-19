# Proposal: Rebuild the SDK aligned to `openapi_spec_v1.json` (AI-maintained)

**Status:** Implemented — the SDK now models all 150 v1 operations; the source of
truth is `tests/Fixtures/openapi_spec_v1.json` and `OpenApiCoverageTest` enforces
it. This document is retained as the design record.
**Author:** —
**Target repo:** internal/libraries/hyperswitch-sdk

## Summary

The SDK's documented source of truth is a pinned Postman/Swagger export
(`tests/Fixtures/postman.json`), on the premise — stated in `README.md`,
`docs/conventions.md`, and `tools/ai-maintenance-prompt.md` — that *"HyperSwitch
does not publish a public OpenAPI specification."*

**That premise is false.** HyperSwitch publishes two OpenAPI specs:

- [`api-reference/v1/openapi_spec_v1.json`](https://github.com/juspay/hyperswitch/blob/main/api-reference/v1/openapi_spec_v1.json) — 120 paths / **149 operations**, 877 schemas. Covers the v1 surface this SDK models.
- [`api-reference/v2/openapi_spec_v2.json`](https://github.com/juspay/hyperswitch/blob/main/api-reference/v2/openapi_spec_v2.json) — v2 intent-based API. **Out of scope** (future separate SDK — see below).

This proposes **rebuilding the SDK against `openapi_spec_v1.json`** and re-pointing
the existing **AI-maintenance workflow** at the OpenAPI spec — exactly the
mechanical, drift-driven, AI-generated model the repo already uses for Postman,
but on a richer, machine-authored, versioned source of truth.

## Why the OpenAPI spec is a strictly better source of truth

| Property | Postman export | OpenAPI v1 spec |
|---|---|---|
| Published/maintained by Juspay | Community export, unversioned | First-party, versioned in the hyperswitch repo |
| Request/response **schemas** | Example bodies only | 877 typed component schemas with `$ref` reuse |
| **Enums** | Inferred from examples | Declared (`enum`, `oneOf`, discriminators) |
| **Auth** per operation | Implicit | `security` + `securitySchemes` (exact header formats) |
| Stable machine identifiers | Item names (prose) | `operationId` per operation |
| Coverage checking | Ad-hoc | Deterministic walk of `paths × methods` and `components.schemas` |

The AI maintenance loop gets *more* mechanical, not less: enums, DTO field types,
required/optional, and auth tier all become derivable instead of guessed.

## Scope

**In scope**
- Re-point source of truth: `postman.json` → `openapi_spec_v1.json`.
- Rebuild/reconcile the Endpoint / DTO / Enum / Resource surface against the spec.
- Rewrite the AI-maintenance workflow (prompt, fetch script, drift test) to pivot on OpenAPI.
- Fix confirmed drift; document deliberate exclusions.
- Update docs to remove the "no public OpenAPI spec" claim.

**Out of scope (future, separate SDK)**
- The **v2** API (`openapi_spec_v2.json`): intent-based payments, `organizations`,
  `profiles`, `connector-accounts`, `routing-algorithms`, `tokenize`,
  `process-trackers`. This is a new SDK, not a refactor of this one. The 13
  `/v1/…`-prefixed paths currently in this SDK (payment-methods,
  payment-method-sessions, proxy, saved-payment-methods) are actually **v2-spec
  compat paths** and belong to that effort — see Group A1.

## Gap analysis — SDK vs `openapi_spec_v1.json`

Method: extract SDK endpoints from `@endpoint` docblocks; extract spec operations
from `paths × methods`; normalize path params (`{id}`, `{payment_id}` → `{}`) and diff.

| Metric | Count |
|---|---|
| SDK endpoints | 83 |
| v1 spec operations | 149 |
| **Present in both** | **65** |
| **In spec, missing from SDK** (coverage gaps) | **84** |
| **In SDK, not in spec** | **18** (13 v2-compat + 5 genuine drift) |

Current coverage: ~44% of the v1 surface.

### Group A — 18 SDK endpoints not in v1 spec

**A1. v2-spec compat paths (13)** — belong to the future v2 SDK; confirmed present in
`openapi_spec_v2.json`. Decide: leave in place (pragmatic) or extract later.
`/v1/payment-methods*`, `/v1/payment-method-sessions*`, `/v1/proxy`,
`/v1/customers/{id}/saved-payment-methods`, `/v1/payment-methods/token/{token}/details`.

**A2. Genuine v1 drift (5)** — reconcile/fix:

| SDK endpoint | Issue | Action |
|---|---|---|
| `PUT /payouts/{id}` | Spec uses **`POST /payouts/{payout_id}`** for update | Fix method to POST |
| `GET /subscriptions/list` | Not in spec (spec has `/subscriptions/items`, `/subscriptions/estimate`, `GET /subscriptions/{id}`) | Rename to `/subscriptions/items` or drop |
| `GET /subscriptions/plans` | Not in spec | Verify vs sandbox; drop or document |
| `POST /api/organization/{id}/convert_to_platform` | Not in spec (spec: `/organization`, `/organization/{id}`, `/user/create_platform`) | Reconcile with Platform group |
| `POST /ephemeral_keys` | Not in v1 spec paths | Verify vs sandbox; document if real-but-undocumented |

### Group B — 84 v1 spec operations missing from SDK

By area (full list in Appendix):

| Area | Missing | Notes |
|---|---|---|
| Routing | 13 | New group (`/routing/*`) |
| Account / Business Profile | 9 | Incl. 7 `dynamic_routing/*`, `webhooks`, `kv` |
| Disputes | 8 | `aggregate`, `filter`, `profile/*`, `evidence` GET/PUT/DELETE |
| Authentication | 8 | New group (3DS/authn) |
| Payments | 7 | `complete_authorize`, `3ds/authentication`, `eligibility`, `post_session_tokens`, `update_metadata`, `GET …/cancel_post_capture` |
| Blocklist | 7 | New group |
| Payouts | 4 | `list`, `filter`, `POST /payouts/{id}` (update — see A2) |
| Legacy Payment Methods | 4 | `/payment_methods/*` — currently **deliberately excluded** (README); revisit |
| GSM | 4 | New group |
| Events / Webhooks (mgmt) | 4 | `attempts`, `retry`, `profile/list`, `POST /events/{merchant_id}` |
| Card Issuers | 4 | New group (spec adds `Card Issuer` tag) |
| Profile Acquirers | 2 | New group |
| Organization | 2 | `GET`/`PUT /organization/{id}` |
| Customers | 2 | `/customers/{id}/payment_methods`, `/customers/payment_methods` |
| Misc | 6 | `poll/status`, `three_ds_decision/execute`, `GET /v1/customers/reference/{id}`, `GET /account/payment_methods`, `POST /{customer_id}/payment_methods/{pm_id}/default` |

### Auth

v1 spec security schemes: `admin_api_key`, `api_key`, `ephemeral_key`, `jwt_key`,
`publishable_key`. The SDK has AuthTiers for all but **`jwt_key`** (bearer JWT) —
add if any adopted operation requires it. Validate the exact wire-format strings
against each scheme's description during reconciliation.

## Mechanical mapping: OpenAPI → SDK

The conventions in `docs/conventions.md` port directly; the source identifiers change
from Postman item names to OpenAPI constructs:

| OpenAPI construct | SDK artifact |
|---|---|
| `tags[]` (e.g. `Payments`, `Refunds`, `Routing`) | Group → `src/Endpoint/<Group>Endpoints.php`, `src/Dto/<Group>/`, `src/Resource/<Group>Resource.php` |
| `operationId` (stable, machine id) | Method / DTO base name (`@openapi <operationId>`) |
| `METHOD` + path | `Endpoint::method` / `pathTemplate` (`@endpoint METHOD /path`) |
| `security[]` → `securitySchemes` | `AuthTier` (+ new `JWT` tier if needed) |
| `requestBody.content.…schema` (`$ref`) | Request DTO |
| `responses.2xx.…schema` (`$ref`) | Response DTO |
| Component schema with `enum` | `src/Enum/<Name>.php` |
| `$ref`-shared schemas (Address, Amount, …) | `src/Dto/Common/` |
| `x-*` / open shapes (`metadata`, connector blobs) | `?array<string,mixed>` (unchanged rule) |

**Frontmatter change** — replace `@postman <Group> / <Item>` with:
```php
/**
 * @openapi   <operationId>            (source of truth)
 * @endpoint  <METHOD> <path>
 * @hyperswitch-source  <spec version / commit>
 */
```

## Tooling changes (the AI-maintenance loop, re-pointed)

1. **`tests/Fixtures/openapi_spec_v1.json`** — pin the spec (replaces `postman.json`; keep the latter until migration completes for cross-check).
2. **`tests/Drift/OpenApiCoverageTest.php`** — replaces `PostmanCoverageTest.php`. Walks `paths × methods` (normalizing path params) and asserts every non-excluded operation has a matching `Endpoint` factory; optionally walks `components.schemas` for DTO/enum coverage. Reads an explicit **exclusion allow-list** so "missing" vs "intentionally excluded" is mechanical.
3. **`tools/update-openapi.sh`** — replaces `update-postman.sh`; fetches + pins the spec at a known commit/tag.
4. **`tools/ai-maintenance-prompt.md`** — rewrite: source of truth is `openapi_spec_v1.json`; the AI reads the drift output + the referenced `operationId`/schema and generates Endpoint + Request/Response DTOs + enums + tests per `docs/conventions.md`. Delete the "no public OpenAPI spec" note.
5. **`composer.json`** — `test:drift` unchanged as a script name; it now runs the OpenAPI coverage suite.

## Work breakdown

1. **Tooling swap** — pin spec, write `OpenApiCoverageTest`, `update-openapi.sh`, exclusion allow-list. Green with today's surface + documented exclusions.
2. **Fix Group A2 drift** (5 items) — payouts method; reconcile subscriptions/organization/ephemeral against sandbox.
3. **Define exclusion list** — codify deliberate omissions (legacy `/payment_methods/*`, admin `/gsm/*`, `/blocklist/*`, internal routing) as data the drift test reads.
4. **Close coverage gaps (Group B)** — one sub-issue per area, AI-generated per the rewritten prompt, prioritized:
   - **P1 (merchant-facing):** Payments extras, Payouts list/filter/update, Disputes filters/evidence, Customers payment_methods, Organization get/update.
   - **P2:** Routing, Events/Webhooks mgmt, Authentication.
   - **P3 (admin/ops):** Blocklist, GSM, Card Issuers, Profile Acquirers, dynamic_routing, poll, three_ds_decision.
5. **Docs** — `README.md` (drop the false claim; name the v1 spec as source of truth; flag v2 as future separate SDK; update coverage table), `docs/conventions.md` (frontmatter + mapping table), `docs/architecture.md`, `tools/ai-maintenance-prompt.md`.
6. **Auth** — add `JWT` tier if any adopted operation needs it; validate wire formats vs `securitySchemes`.

## Acceptance criteria

- [ ] `composer test:drift` runs against `openapi_spec_v1.json` (not Postman) and is green with a documented exclusion allow-list.
- [ ] All Group A2 drift resolved (payouts method fixed; subscriptions/org/ephemeral reconciled or documented).
- [ ] P1 gaps closed with DTOs + tests; P2/P3 tracked as follow-up sub-issues.
- [ ] `tools/ai-maintenance-prompt.md` pivots on the OpenAPI spec; no doc claims "no public OpenAPI spec exists".
- [ ] `composer ci` green.

## Migration strategy

Keep `postman.json` and the Postman drift test alongside the new OpenAPI test for **one
transition PR**, so coverage can be cross-checked, then delete the Postman path. No
public API of the SDK changes for the 65 already-covered operations — this is additive
plus the 5 A2 fixes.

---

## Appendix — full endpoint lists

<details><summary>Group A: 18 SDK endpoints not in v1 spec</summary>

```
# A1 — v2-spec compat (out of scope; future v2 SDK)
DELETE /v1/payment-method-sessions/{id}
DELETE /v1/payment-methods/{id}
GET    /v1/customers/{id}/saved-payment-methods
GET    /v1/payment-method-sessions/{id}
GET    /v1/payment-method-sessions/{id}/list-payment-methods
GET    /v1/payment-methods/{id}
GET    /v1/payment-methods/token/{token}/details
POST   /v1/payment-method-sessions
POST   /v1/payment-method-sessions/{id}/confirm
POST   /v1/payment-methods
POST   /v1/proxy
PUT    /v1/payment-method-sessions/{id}/update-saved-payment-method
PUT    /v1/payment-methods/{id}/update-saved-payment-method

# A2 — genuine v1 drift (fix)
GET    /subscriptions/list
GET    /subscriptions/plans
POST   /api/organization/{id}/convert_to_platform
POST   /ephemeral_keys
PUT    /payouts/{id}
```
</details>

<details><summary>Group B: 84 v1 spec operations missing from SDK</summary>

```
DELETE /blocklist
DELETE /card_issuers/{id}
DELETE /disputes/evidence
DELETE /payment_methods/{id}
GET    /account/payment_methods
GET    /authentication/{id}/eligibility-check
GET    /blocklist
GET    /blocklist/batch
GET    /blocklist/batch/{job_id}
GET    /card_issuers
GET    /customers/{id}/payment_methods
GET    /customers/payment_methods
GET    /disputes/aggregate
GET    /disputes/evidence/{dispute_id}
GET    /disputes/filter
GET    /disputes/profile/aggregate
GET    /disputes/profile/filter
GET    /disputes/profile/list
GET    /events/{merchant_id}/{event_id}/attempts
GET    /organization/{id}
GET    /payment_methods/{id}
GET    /payments/{id}/cancel_post_capture
GET    /payouts/list
GET    /poll/status/{poll_id}
GET    /routing
GET    /routing/{id}
GET    /routing/active
GET    /routing/default
GET    /routing/default/profile
GET    /subscriptions/items
GET    /v1/customers/reference/{merchant_reference_id}
PATCH  /account/{id}/business_profile/{profile_id}/dynamic_routing/contracts/config/{algorithm_id}
PATCH  /account/{id}/business_profile/{profile_id}/dynamic_routing/success_based/config/{algorithm_id}
POST   /{customer_id}/payment_methods/{payment_method_id}/default
POST   /account/{id}/business_profile/{profile_id}/dynamic_routing/contracts/toggle
POST   /account/{id}/business_profile/{profile_id}/dynamic_routing/elimination/create
POST   /account/{id}/business_profile/{profile_id}/dynamic_routing/elimination/toggle
POST   /account/{id}/business_profile/{profile_id}/dynamic_routing/success_based/create
POST   /account/{id}/business_profile/{profile_id}/dynamic_routing/success_based/toggle
POST   /account/{id}/webhooks/{merchant_connector_id}
POST   /accounts/{id}/kv
POST   /authentication
POST   /authentication/{id}/authenticate
POST   /authentication/{id}/eligibility
POST   /authentication/{id}/eligibility-check
POST   /authentication/{id}/enabled_authn_methods_token
POST   /authentication/{id}/redirect
POST   /authentication/{id}/sync
POST   /blocklist
POST   /blocklist/batch
POST   /blocklist/toggle
POST   /card_issuers
POST   /events/{merchant_id}
POST   /events/{merchant_id}/{event_id}/retry
POST   /events/profile/list
POST   /gsm
POST   /gsm/delete
POST   /gsm/get
POST   /gsm/update
POST   /payment_methods
POST   /payment_methods/{id}/update
POST   /payments/{id}/3ds/authentication
POST   /payments/{id}/complete_authorize
POST   /payments/{id}/eligibility
POST   /payments/{id}/eligibility_check
POST   /payments/{id}/post_session_tokens
POST   /payments/{id}/update_metadata
POST   /payouts/{id}
POST   /payouts/filter
POST   /payouts/list
POST   /profile_acquirers
POST   /profile_acquirers/{profile_id}/{profile_acquirer_id}
POST   /routing
POST   /routing/{id}/activate
POST   /routing/deactivate
POST   /routing/default
POST   /routing/default/profile/{profile_id}
POST   /routing/evaluate
POST   /routing/feedback
POST   /routing/rule/evaluate
POST   /three_ds_decision/execute
PUT    /card_issuers/{id}
PUT    /disputes/evidence
PUT    /organization/{id}
```
</details>
