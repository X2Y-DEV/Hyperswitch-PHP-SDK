<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Auth;

final class MerchantKeyAuthResolver implements AuthContextResolverInterface
{
    public function __construct(private readonly string $apiKey)
    {
    }

    public function tier(): AuthTier
    {
        return AuthTier::MERCHANT;
    }

    public function headers(): array
    {
        return ['api-key' => $this->apiKey];
    }
}
