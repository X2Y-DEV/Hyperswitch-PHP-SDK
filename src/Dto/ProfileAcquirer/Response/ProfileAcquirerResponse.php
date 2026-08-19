<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Create a Profile Acquirer"
 * @endpoint            POST /profile_acquirers
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ProfileAcquirerResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $profileAcquirerId,
        public readonly string $profileId,
        public readonly bool $isDefault,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['profile_acquirer_id', 'profile_id', 'is_default'];

        return new self(
            profileAcquirerId: (string) $data['profile_acquirer_id'],
            profileId:         (string) ($data['profile_id'] ?? ''),
            isDefault:         (bool) ($data['is_default'] ?? false),
            extras:            array_diff_key($data, array_flip($known)),
        );
    }
}
