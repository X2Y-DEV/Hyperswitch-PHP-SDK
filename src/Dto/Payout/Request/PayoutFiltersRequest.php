<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Body for POST /payouts/filter — a time range for which available filters are
 * requested. (v1 spec schema: TimeRange.)
 *
 * @openapi             "List available payout filters"
 * @endpoint            POST /payouts/filter
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PayoutFiltersRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly \DateTimeImmutable $startTime,
        public readonly ?\DateTimeImmutable $endTime = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'start_time' => $this->startTime->format(\DateTimeInterface::ATOM),
            'end_time'   => $this->endTime?->format(\DateTimeInterface::ATOM),
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
