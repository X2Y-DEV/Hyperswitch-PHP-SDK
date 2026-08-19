<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionChargesHandling: string implements LabelledEnum
{
    case INVOICE_IMMEDIATELY    = 'invoice_immediately';
    case ADD_TO_UNBILLED_CHARGES = 'add_to_unbilled_charges';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE_IMMEDIATELY     => 'Invoice immediately',
            self::ADD_TO_UNBILLED_CHARGES => 'Add to unbilled charges',
        };
    }
}
