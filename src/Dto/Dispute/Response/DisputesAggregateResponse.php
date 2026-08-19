<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Get Disputes Aggregate"
 * @endpoint            GET /disputes/aggregate
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class DisputesAggregateResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $statusWithCount Map of dispute status → count.
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?array $statusWithCount = null,
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
            statusWithCount: self::objectField($data, 'status_with_count'),
            extras:          array_diff_key($data, array_flip(['status_with_count'])),
        );
    }
}
