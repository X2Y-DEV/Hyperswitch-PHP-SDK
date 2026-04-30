<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Platform\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * @postman             Platform / Platform - Conversion
 * @endpoint            POST /api/organization/{organization_id}/convert_to_platform
 * @hyperswitch-source  2026-04-30
 */
final class ConvertToPlatformRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $platformMerchantName,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'platform_merchant_name' => $this->platformMerchantName,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
