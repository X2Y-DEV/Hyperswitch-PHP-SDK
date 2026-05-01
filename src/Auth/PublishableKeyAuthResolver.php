<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Auth;

final class PublishableKeyAuthResolver implements AuthContextResolverInterface
{
    public function __construct(private readonly string $publishableKey)
    {
    }

    public function tier(): AuthTier
    {
        return AuthTier::PUBLISHABLE;
    }

    public function headers(): array
    {
        return ['api-key' => $this->publishableKey];
    }
}
