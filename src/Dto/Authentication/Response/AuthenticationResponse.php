<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Authentication\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Authentication object returned by create / authenticate / eligibility / sync.
 * Tolerant shape — the connector-specific 3DS fields land in `extras`.
 *
 * @openapi             "Create an Authentication"
 * @endpoint            POST /authentication
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class AuthenticationResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $authenticationId = null,
        public readonly ?string $status = null,
        public readonly ?string $clientSecret = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['authentication_id', 'status', 'client_secret'];

        return new self(
            authenticationId: isset($data['authentication_id']) ? (string) $data['authentication_id'] : null,
            status:           isset($data['status']) ? (string) $data['status'] : null,
            clientSecret:     isset($data['client_secret']) ? (string) $data['client_secret'] : null,
            extras:           array_diff_key($data, array_flip($known)),
        );
    }
}
