<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Platform\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Update an Organization"
 * @endpoint            PUT /organization/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class OrganizationUpdateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $organizationDetails
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $platformMerchantId,
        public readonly ?string $organizationName = null,
        public readonly ?array $organizationDetails = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'platform_merchant_id' => $this->platformMerchantId,
            'organization_name'    => $this->organizationName,
            'organization_details' => $this->organizationDetails,
            'metadata'             => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
