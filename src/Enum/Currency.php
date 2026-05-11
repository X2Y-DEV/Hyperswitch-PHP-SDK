<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * ISO 4217 currency codes. Stub set; the AI maintenance pass populates
 * the full list from HyperSwitch's accepted currencies.
 *
 * @hyperswitch-source 2026-04-30
 */
enum Currency: string implements LabelledEnum
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case JPY = 'JPY';
    case INR = 'INR';
    case ZAR = 'ZAR';

    public function label(): string
    {
        return match ($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::GBP => 'Pound Sterling',
            self::CAD => 'Canadian Dollar',
            self::AUD => 'Australian Dollar',
            self::JPY => 'Japanese Yen',
            self::INR => 'Indian Rupee',
            self::ZAR => 'South African rand',
        };
    }
}
