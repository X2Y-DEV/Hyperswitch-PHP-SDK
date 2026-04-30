<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Subscription\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Enum\SubscriptionCancelOption;
use X2Y\HyperswitchClient\Enum\SubscriptionCreditOption;
use X2Y\HyperswitchClient\Enum\SubscriptionRefundableCreditsHandling;
use X2Y\HyperswitchClient\Enum\SubscriptionUnbilledChargesOption;

/**
 * @postman             Subscriptions / Subscription - Cancel
 * @endpoint            POST /subscriptions/{id}/cancel
 * @hyperswitch-source  2026-04-30
 */
final class CancelSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly SubscriptionCancelOption $cancelOption = SubscriptionCancelOption::IMMEDIATELY,
        public readonly ?\DateTimeImmutable $cancelDate = null,
        public readonly ?SubscriptionUnbilledChargesOption $unbilledChargesOption = null,
        public readonly ?SubscriptionCreditOption $creditOptionForCurrentTermCharges = null,
        public readonly ?SubscriptionRefundableCreditsHandling $refundableCreditsHandling = null,
        public readonly ?string $cancelReasonCode = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'cancel_option'                          => $this->cancelOption->value,
            'cancel_date'                            => $this->cancelDate?->format(\DateTimeInterface::ATOM),
            'unbilled_charges_option'                => $this->unbilledChargesOption?->value,
            'credit_option_for_current_term_charges' => $this->creditOptionForCurrentTermCharges?->value,
            'refundable_credits_handling'            => $this->refundableCreditsHandling?->value,
            'cancel_reason_code'                     => $this->cancelReasonCode,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
