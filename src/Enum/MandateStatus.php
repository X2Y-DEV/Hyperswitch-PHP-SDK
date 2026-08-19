<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum MandateStatus: string implements LabelledEnum
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case PENDING  = 'pending';
    case REVOKED  = 'revoked';
    case EXPIRED  = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE   => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING  => 'Pending',
            self::REVOKED  => 'Revoked',
            self::EXPIRED  => 'Expired',
        };
    }
}
