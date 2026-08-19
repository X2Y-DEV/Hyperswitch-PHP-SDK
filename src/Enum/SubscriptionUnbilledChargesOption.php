<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionUnbilledChargesOption: string implements LabelledEnum
{
    case INVOICE = 'invoice';
    case DELETE  = 'delete';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE => 'Invoice unbilled charges',
            self::DELETE  => 'Discard unbilled charges',
        };
    }
}
