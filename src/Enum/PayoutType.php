<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum PayoutType: string implements LabelledEnum
{
    case CARD = 'card';
    case BANK = 'bank';
    case WALLET = 'wallet';

    public function label(): string
    {
        return match ($this) {
            self::CARD   => 'Card',
            self::BANK   => 'Bank account',
            self::WALLET => 'Wallet',
        };
    }
}
