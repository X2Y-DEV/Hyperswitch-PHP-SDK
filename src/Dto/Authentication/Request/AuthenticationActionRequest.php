<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Authentication\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Passthrough body for the per-authentication 3DS actions (authenticate,
 * eligibility, eligibility-check, sync, redirect, session token). These bodies
 * are connector- and device-specific 3DS payloads, so the SDK keeps them as an
 * opaque `extras` passthrough per the connector-blob convention rather than
 * duplicating a near-identical typed DTO per action.
 *
 * @openapi             tag: Authentication (per-authentication action body)
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class AuthenticationActionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $clientSecret = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls(['client_secret' => $this->clientSecret]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
