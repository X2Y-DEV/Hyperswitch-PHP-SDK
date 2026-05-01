<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Subscriptions / Subscription - Cancel (refundable_credits_handling)
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionRefundableCreditsHandling: string implements LabelledEnum
{
    case NO_ACTION       = 'no_action';
    case SCHEDULE_REFUND = 'schedule_refund';

    public function label(): string
    {
        return match ($this) {
            self::NO_ACTION       => 'Take no action on refundable credits',
            self::SCHEDULE_REFUND => 'Schedule refund of refundable credits',
        };
    }
}
