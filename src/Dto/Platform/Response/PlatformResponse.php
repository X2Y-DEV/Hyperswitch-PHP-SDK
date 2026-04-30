<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Platform\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;

/**
 * Generic response from Platform-group endpoints. Each endpoint returns a
 * different shape (Organization, Platform user, conversion ack) — the
 * fields are loosely typed; deeper data passes through `extras`.
 *
 * @postman             Platform / Organization - Create  (and others)
 * @hyperswitch-source  2026-04-30
 */
final class PlatformResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $organizationId = null,
        public readonly ?string $organizationName = null,
        public readonly ?string $merchantId = null,
        public readonly ?string $userId = null,
        public readonly ?bool $isPlatform = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'organization_id', 'organization_name', 'merchant_id', 'user_id', 'is_platform',
        ];

        return new self(
            organizationId:   isset($data['organization_id'])   ? (string) $data['organization_id']   : null,
            organizationName: isset($data['organization_name']) ? (string) $data['organization_name'] : null,
            merchantId:       isset($data['merchant_id'])       ? (string) $data['merchant_id']       : null,
            userId:           isset($data['user_id'])           ? (string) $data['user_id']           : null,
            isPlatform:       isset($data['is_platform'])       ? (bool)   $data['is_platform']       : null,
            extras:           array_diff_key($data, array_flip($known)),
        );
    }
}
