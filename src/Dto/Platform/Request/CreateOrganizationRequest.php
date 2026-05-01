<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Platform\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @postman             Platform / Organization - Create
 * @endpoint            POST /organization
 * @hyperswitch-source  2026-04-30
 */
final class CreateOrganizationRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $organizationDetails
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $organizationName,
        public readonly ?array $organizationDetails = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'organization_name'    => $this->organizationName,
            'organization_details' => $this->organizationDetails,
            'metadata'             => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
