<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum SetupFutureUsage: string implements LabelledEnum
{
    case OFF_SESSION = 'off_session';
    case ON_SESSION  = 'on_session';

    public function label(): string
    {
        return match ($this) {
            self::OFF_SESSION => 'Off-session (merchant-initiated future use)',
            self::ON_SESSION  => 'On-session (customer-initiated future use)',
        };
    }
}
