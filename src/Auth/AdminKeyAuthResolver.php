<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Auth;

final class AdminKeyAuthResolver implements AuthContextResolverInterface
{
    public function __construct(private readonly string $apiKey)
    {
    }

    public function tier(): AuthTier
    {
        return AuthTier::ADMIN;
    }

    public function headers(): array
    {
        return ['api-key' => $this->apiKey];
    }
}
