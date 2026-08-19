<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Routing\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Body for dynamic-routing create/update/toggle operations (success-based,
 * contract-based, elimination). The config shape differs per algorithm and
 * connector, so the whole body is a passthrough blob via `extras`.
 *
 * @openapi             tag: Routing (dynamic routing config)
 * @endpoint            POST /account/{id}/business_profile/{id}/dynamic_routing/*
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class DynamicRoutingConfigRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
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
