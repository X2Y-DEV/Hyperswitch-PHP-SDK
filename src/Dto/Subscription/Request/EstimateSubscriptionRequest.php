<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Estimate the cost of a subscription before creating it (proration, taxes,
 * etc.). Sent as query parameters.
 *
 * @endpoint            GET /subscriptions/estimate
 * @hyperswitch-source  2026-04-30
 */
final class EstimateSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $itemPriceId,
        public readonly ?string $customerId = null,
        public readonly ?int $quantity = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'item_price_id' => $this->itemPriceId,
            'customer_id'   => $this->customerId,
            'quantity'      => $this->quantity,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
