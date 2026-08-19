<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Routing\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Create/deactivate a routing config. The `algorithm` payload is a
 * connector/rule blob that varies per routing kind, so it stays
 * `?array<string, mixed>`.
 *
 * @openapi             "Create a routing config"
 * @endpoint            POST /routing
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class RoutingConfigRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $algorithm
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $profileId = null,
        public readonly ?array $algorithm = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'name'        => $this->name,
            'description' => $this->description,
            'profile_id'  => $this->profileId,
            'algorithm'   => $this->algorithm,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
