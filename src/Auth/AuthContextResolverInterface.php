<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Auth;

/**
 * Returns the headers a request needs for a given auth tier.
 *
 * Implementations are stateless and reusable — instantiated once per tier
 * and registered with the HyperswitchClient.
 */
interface AuthContextResolverInterface
{
    public function tier(): AuthTier;

    /**
     * @return array<string, string> headers to merge onto the outbound request
     */
    public function headers(): array;
}
