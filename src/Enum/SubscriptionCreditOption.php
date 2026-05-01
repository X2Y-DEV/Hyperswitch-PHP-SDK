<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * Credit handling for current-term charges on cancellation.
 *
 * @postman             Subscriptions / Subscription - Cancel (credit_option_for_current_term_charges)
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionCreditOption: string implements LabelledEnum
{
    case NONE    = 'none';
    case PRORATE = 'prorate';
    case FULL    = 'full';

    public function label(): string
    {
        return match ($this) {
            self::NONE    => 'No credit',
            self::PRORATE => 'Prorated credit',
            self::FULL    => 'Full credit',
        };
    }
}
