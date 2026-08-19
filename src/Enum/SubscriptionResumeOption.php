<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionResumeOption: string implements LabelledEnum
{
    case IMMEDIATELY   = 'immediately';
    case SPECIFIC_DATE = 'specific_date';

    public function label(): string
    {
        return match ($this) {
            self::IMMEDIATELY   => 'Immediately',
            self::SPECIFIC_DATE => 'On a specific date',
        };
    }
}
