<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Authentication\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Create an Authentication"
 * @endpoint            POST /authentication
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class AuthenticationCreateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $profileId = null,
        public readonly ?string $authenticationConnector = null,
        public readonly ?string $returnUrl = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'profile_id'               => $this->profileId,
            'authentication_connector' => $this->authenticationConnector,
            'return_url'               => $this->returnUrl,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
