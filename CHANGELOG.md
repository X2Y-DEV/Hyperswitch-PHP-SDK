# Changelog

All notable changes to `x2y/hyperswitch-client` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Each release records the `@hyperswitch-source` date the SDK was reconciled against.

## [Unreleased]

### Added
- Initial repository scaffolding (composer, phpstan, phpunit, CI, conventions).
- Transport layer: `Endpoint`, `ApiClient`, `PathBinder`, middleware pipeline.
- Auth layer: `AuthTier` enum + four resolver implementations.
- DTO base classes: `AbstractRequestDto`, `AbstractResponseDto` with extras passthrough.
- Exception hierarchy.
- Payments vertical-slice template (Endpoint, Resource, Create request, Payment response).
- Drift test scaffold (`tests/Drift/PostmanCoverageTest`).
- AI maintenance prompt + conventions documentation.

### Source
- `@hyperswitch-source: 2026-04-30` (initial Postman export pinned in `tests/Fixtures/postman.json`).
