<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Relay / Relay - Create (type)
 * @hyperswitch-source  2026-04-30
 */
enum RelayType: string implements LabelledEnum
{
    case REFUND                    = 'refund';
    case CAPTURE                   = 'capture';
    case INCREMENTAL_AUTHORIZATION = 'incremental_authorization';
    case VOID                      = 'void';

    public function label(): string
    {
        return match ($this) {
            self::REFUND                    => 'Refund',
            self::CAPTURE                   => 'Capture',
            self::INCREMENTAL_AUTHORIZATION => 'Incremental authorization',
            self::VOID                      => 'Void',
        };
    }
}
