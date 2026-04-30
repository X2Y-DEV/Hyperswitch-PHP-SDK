<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * @postman             Subscriptions / Subscription - Pause (pause_option)
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionPauseOption: string implements LabelledEnum
{
    case IMMEDIATELY  = 'immediately';
    case END_OF_TERM  = 'end_of_term';
    case SPECIFIC_DATE = 'specific_date';

    public function label(): string
    {
        return match ($this) {
            self::IMMEDIATELY   => 'Immediately',
            self::END_OF_TERM   => 'End of current term',
            self::SPECIFIC_DATE => 'On a specific date',
        };
    }
}
