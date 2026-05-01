<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Payments (Mandates / mandate_data.mandate_type)
 * @hyperswitch-source  2026-04-30
 */
enum MandateType: string implements LabelledEnum
{
    case SINGLE_USE = 'single_use';
    case MULTI_USE  = 'multi_use';

    public function label(): string
    {
        return match ($this) {
            self::SINGLE_USE => 'Single use',
            self::MULTI_USE  => 'Multi use',
        };
    }
}
