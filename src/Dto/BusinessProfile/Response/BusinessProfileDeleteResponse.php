<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\BusinessProfile\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;

/**
 * @postman             Business Profile / Business Profile - Delete
 * @endpoint            DELETE /account/{merchant_id}/business_profile/{profile_id}
 * @hyperswitch-source  2026-04-30
 */
final class BusinessProfileDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $profileId,
        public readonly bool $deleted,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['profile_id', 'deleted'];

        return new self(
            profileId: (string) ($data['profile_id'] ?? ''),
            deleted:   (bool)   ($data['deleted'] ?? false),
            extras:    array_diff_key($data, array_flip($known)),
        );
    }
}
