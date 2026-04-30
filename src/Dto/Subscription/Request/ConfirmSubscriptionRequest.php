<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Subscription\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * Confirm a previously-created subscription by attaching payment details.
 *
 * @postman             Subscriptions / Subscription - Confirm
 * @endpoint            POST /subscriptions/{id}/confirm
 * @hyperswitch-source  2026-04-30
 */
final class ConfirmSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $paymentDetails  payment_method_data + customer_acceptance
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly array $paymentDetails,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_details' => $this->paymentDetails,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
