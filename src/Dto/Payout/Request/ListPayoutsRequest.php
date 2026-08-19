<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * List payouts with generic constraints. Sent as query parameters on a GET
 * request — the Resource passes `toArray()` as `queryParams`.
 *
 * @openapi             "List payouts using generic constraints"
 * @endpoint            GET /payouts/list
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ListPayoutsRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras Additional constraints (e.g. created, time_range).
     */
    public function __construct(
        public readonly ?string $customerId = null,
        public readonly ?string $startingAfter = null,
        public readonly ?string $endingBefore = null,
        public readonly ?int $limit = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'customer_id'    => $this->customerId,
            'starting_after' => $this->startingAfter,
            'ending_before'  => $this->endingBefore,
            'limit'          => $this->limit,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
