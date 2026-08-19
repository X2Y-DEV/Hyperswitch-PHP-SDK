<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Update a Profile Acquirer"
 * @endpoint            POST /profile_acquirers/{id}/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ProfileAcquirerUpdateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras Fields to update (network, acquirer_bin, is_default, …).
     */
    public function __construct(
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        return $this->mergeExtras([], $this->extras);
    }
}
