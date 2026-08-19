<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Platform\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @endpoint            POST /user/create_platform
 * @hyperswitch-source  2026-04-30
 */
final class CreatePlatformUserRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $organizationName,
        public readonly ?string $merchantHome = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'organization_name' => $this->organizationName,
            'merchant_home'     => $this->merchantHome,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
