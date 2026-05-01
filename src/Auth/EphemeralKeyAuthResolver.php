<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Auth;

/**
 * Ephemeral keys are short-lived, per-customer keys minted by the merchant
 * server and handed to the client for restricted-scope payment-method calls.
 *
 * The resolver expects the consumer to supply the already-minted ephemeral
 * key string. Lifecycle (mint, rotate, expire) is the consumer's concern.
 */
final class EphemeralKeyAuthResolver implements AuthContextResolverInterface
{
    public function __construct(private readonly string $ephemeralKey)
    {
    }

    public function tier(): AuthTier
    {
        return AuthTier::EPHEMERAL;
    }

    public function headers(): array
    {
        return ['api-key' => $this->ephemeralKey];
    }
}
