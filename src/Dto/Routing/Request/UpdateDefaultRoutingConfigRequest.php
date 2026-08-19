<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Routing\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Update the default fallback routing config. The v1 endpoints accept a
 * top-level JSON ARRAY of routable connector choices (not an object), so
 * `toArray()` returns the list directly.
 *
 * @openapi             "Update default fallback config"
 * @endpoint            POST /routing/default
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class UpdateDefaultRoutingConfigRequest extends AbstractRequestDto
{
    /**
     * @param list<array<string, mixed>> $connectors Ordered routable connector choices.
     */
    public function __construct(
        public readonly array $connectors = [],
    ) {
    }

    /**
     * This endpoint's body is a top-level JSON array, unlike every other Request
     * DTO (which returns an associative array). Json::encode emits a JSON array.
     *
     * @return list<array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->connectors;
    }
}
