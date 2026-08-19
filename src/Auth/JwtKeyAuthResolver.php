<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Auth;

/**
 * Resolves JWT bearer auth for JWT-scoped endpoints (event management, etc.).
 * HyperSwitch's `jwt_key` security scheme is `http` / `bearer` (JWT), i.e.
 * `Authorization: Bearer <token>`.
 */
final class JwtKeyAuthResolver implements AuthContextResolverInterface
{
    public function __construct(private readonly string $jwt)
    {
    }

    public function tier(): AuthTier
    {
        return AuthTier::JWT;
    }

    public function headers(): array
    {
        return ['Authorization' => 'Bearer ' . $this->jwt];
    }
}
