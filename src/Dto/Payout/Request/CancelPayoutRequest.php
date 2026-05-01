<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @postman             Payouts / Payouts - Cancel
 * @endpoint            POST /payouts/{id}/cancel
 * @hyperswitch-source  2026-04-30
 */
final class CancelPayoutRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $cancellationReason = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'cancellation_reason' => $this->cancellationReason,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
