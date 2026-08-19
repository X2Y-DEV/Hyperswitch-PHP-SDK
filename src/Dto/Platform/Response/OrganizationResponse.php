<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Platform\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * @openapi             "Retrieve an Organization"
 * @endpoint            GET /organization/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class OrganizationResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $organizationDetails
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $organizationId,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $modifiedAt = null,
        public readonly ?string $organizationName = null,
        public readonly ?string $organizationType = null,
        public readonly ?array $organizationDetails = null,
        public readonly ?array $metadata = null,
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
            'organization_id', 'created_at', 'modified_at', 'organization_name',
            'organization_type', 'organization_details', 'metadata',
        ];

        return new self(
            organizationId:      (string) $data['organization_id'],
            createdAt:           DateTimeParser::parse($data['created_at'] ?? null),
            modifiedAt:          DateTimeParser::parse($data['modified_at'] ?? null),
            organizationName:    isset($data['organization_name']) ? (string) $data['organization_name'] : null,
            organizationType:    isset($data['organization_type']) ? (string) $data['organization_type'] : null,
            organizationDetails: self::objectField($data, 'organization_details'),
            metadata:            self::objectField($data, 'metadata'),
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
