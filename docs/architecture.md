# Architecture

The SDK is a thin, layered client for the HyperSwitch HTTP API. Every layer is
replaceable in isolation — Resources don't know about HTTP, the ApiClient
doesn't know about Postman groups, and DTOs don't know about transport.

```
┌───────────────────────────────────────────────────────────┐
│ Resource           Typed facade per Postman group         │
│                    (PaymentsResource, RefundsResource, …) │
├───────────────────────────────────────────────────────────┤
│ Endpoint           Value object: method, path, auth tier, │
│                    request/response DTO classes           │
├───────────────────────────────────────────────────────────┤
│ Dto                Immutable typed Request/Response       │
│                    objects with toArray() / fromArray()   │
├───────────────────────────────────────────────────────────┤
│ Transport          ApiClient + middleware pipeline +      │
│                    PathBinder                             │
├───────────────────────────────────────────────────────────┤
│ Auth               AuthTier enum + per-tier resolver      │
└───────────────────────────────────────────────────────────┘
                            │
                            ▼
                    PSR-18 HTTP client
                    (consumer-supplied)
```

## Layer responsibilities

### Resource

- **What it is:** the public API for SDK consumers. `$client->payments()->create($req)`.
- **What it knows:** which Endpoints exist for its group, and how to call the
  ApiClient.
- **What it does NOT know:** HTTP, framework types, business logic.

### Endpoint

- **What it is:** a value object describing one API call.
- **Carries:** HTTP method, path template, required auth tier, idempotency
  hint, and references to the Request/Response DTO classes.
- **Why it exists:** so callers don't string-build URLs or guess auth headers.

### DTO

- **Request DTO:** immutable, typed, `toArray()` produces the JSON body.
- **Response DTO:** immutable, typed, `fromArray()` constructs from decoded
  JSON. Unknown fields go into `extras` for forward compatibility.
- **Common/:** shared sub-objects (`Address`, `BillingDetails`, `Amount`)
  used across multiple endpoints.

### Transport

- **ApiClient:** the only place that calls the PSR-18 client. Handles
  serialisation, header assembly, error mapping.
- **Endpoint binding:** `PathBinder` substitutes path placeholders and
  appends query strings.
- **Middleware:** onion-style — each wraps the next, sees the outbound
  request and inbound response. Idempotency, retry, logging, error mapping.

### Auth

- **AuthTier enum:** ADMIN, MERCHANT, PUBLISHABLE, EPHEMERAL.
- **AuthContextResolverInterface:** returns headers for a given tier.
- **Wiring:** the consumer instantiates resolvers with their keys and registers
  them with `HyperSwitchClient`. The ApiClient picks the right resolver from
  the Endpoint's declared `authTier`.

## Why this shape

1. **Each Postman item maps to exactly one Endpoint, one Request DTO,
   one Response DTO.** AI maintenance is mechanical: read Postman, generate
   files at predictable paths.
2. **Resources are thin.** Adding a method is a 5-line patch — bind the
   Endpoint, dispatch, return. No business logic to review.
3. **Drift detection is feasible.** A test walks Postman and checks every
   endpoint has a matching Endpoint factory in `src/Endpoint/`. Failure is
   structured: "these endpoints are missing."
4. **Replaceable transport.** The PSR-18 dependency is a contract, not a
   library — consumers pick `symfony/http-client`, `guzzlehttp/guzzle`, or
   their own.
5. **Forward compatibility.** HyperSwitch can add fields to a response without
   breaking the SDK — `extras` preserves them. Bumping minor versions for
   new fields is non-disruptive.

## What the SDK is not

- Not a state machine. No subscription lifecycle, no retry policies beyond
  transport-level transient handling, no smart routing.
- Not a framework integration. The Magento module and any future framework
  integrations live in separate repositories and depend on this one.
- Not a credentials manager. The SDK accepts API keys; rotation, storage,
  and revocation are the consumer's job.
- Not a metering or telemetry layer. The consumer logs what it wants via the
  PSR-3 logger it supplies.

## Versioning

Semver. See [README.md](../README.md#versioning) and
[CHANGELOG.md](../CHANGELOG.md). Each release is tagged with the
`@hyperswitch-source` date it was reconciled against, so consumers can trace
exactly which HyperSwitch API surface a release reflects.
