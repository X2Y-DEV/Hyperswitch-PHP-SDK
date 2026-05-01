<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Change the plan / price tier on an active subscription.
 *
 * @postman             Subscriptions / Subscription - Update
 * @endpoint            PUT /subscriptions/{id}/update
 * @hyperswitch-source  2026-04-30
 */
final class UpdateSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $planId = null,
        public readonly ?string $itemPriceId = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'plan_id'       => $this->planId,
            'item_price_id' => $this->itemPriceId,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
