<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Refunds / Refunds - Create (refund_type)
 * @hyperswitch-source  2026-04-30
 */
enum RefundType: string implements LabelledEnum
{
    case INSTANT   = 'instant';
    case SCHEDULED = 'scheduled';

    public function label(): string
    {
        return match ($this) {
            self::INSTANT   => 'Instant',
            self::SCHEDULED => 'Scheduled',
        };
    }
}
