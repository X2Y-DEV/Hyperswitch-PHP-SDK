<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionUnpaidInvoicesHandling: string implements LabelledEnum
{
    case NO_ACTION                   = 'no_action';
    case SCHEDULE_PAYMENT_COLLECTION = 'schedule_payment_collection';

    public function label(): string
    {
        return match ($this) {
            self::NO_ACTION                   => 'Take no action on unpaid invoices',
            self::SCHEDULE_PAYMENT_COLLECTION => 'Schedule payment collection',
        };
    }
}
