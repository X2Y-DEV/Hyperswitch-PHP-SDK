<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Payments / Payments - Create (authentication_type)
 * @hyperswitch-source  2026-04-30
 */
enum AuthenticationType: string implements LabelledEnum
{
    case THREE_DS    = 'three_ds';
    case NO_THREE_DS = 'no_three_ds';

    public function label(): string
    {
        return match ($this) {
            self::THREE_DS    => '3-D Secure',
            self::NO_THREE_DS => 'No 3-D Secure',
        };
    }
}
