<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\MerchantAccount\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Enable/Disable KV for a Merchant Account"
 * @endpoint            POST /accounts/{id}/kv
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ToggleKvRequest extends AbstractRequestDto
{
    public function __construct(
        public readonly bool $kvEnabled,
    ) {
    }

    public function toArray(): array
    {
        return ['kv_enabled' => $this->kvEnabled];
    }
}
