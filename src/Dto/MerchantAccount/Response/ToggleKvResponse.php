<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\MerchantAccount\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Enable/Disable KV for a Merchant Account"
 * @endpoint            POST /accounts/{id}/kv
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ToggleKvResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $merchantId,
        public readonly bool $kvEnabled,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            merchantId: (string) $data['merchant_id'],
            kvEnabled:  (bool) ($data['kv_enabled'] ?? false),
            extras:     array_diff_key($data, array_flip(['merchant_id', 'kv_enabled'])),
        );
    }
}
