<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Auth;

/**
 * V1 endpoints under `/v1/payment-methods` and `/v1/payment-method-sessions`
 * authenticate the same merchant secret key, but expect it as a value
 * inside the `Authorization` header (formatted `api-key=<key>`) rather
 * than the standard top-level `api-key: <key>` header.
 *
 * Same key value as MerchantKeyAuthResolver — different wire format.
 *
 * V1 endpoints additionally require an `X-Profile-Id` header. The auth
 * resolver does not set it; consumers attach it via
 * `Resource::withProfileId()` so the same V1 client can serve multiple
 * business profiles in the same process.
 */
final class MerchantKeyV1AuthResolver implements AuthContextResolverInterface
{
    public function __construct(private readonly string $apiKey)
    {
    }

    public function tier(): AuthTier
    {
        return AuthTier::MERCHANT_V1;
    }

    public function headers(): array
    {
        return ['Authorization' => 'api-key=' . $this->apiKey];
    }
}
