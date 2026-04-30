<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * @postman             Payouts / Payouts - Retrieve (status)
 * @hyperswitch-source  2026-04-30
 */
enum PayoutStatus: string implements LabelledEnum
{
    case REQUIRES_FULFILLMENT = 'requires_fulfillment';
    case REQUIRES_CONFIRMATION = 'requires_confirmation';
    case REQUIRES_PAYOUT_METHOD_DATA = 'requires_payout_method_data';
    case REQUIRES_VENDOR_ACCOUNT_CREATION = 'requires_vendor_account_creation';
    case INITIATED = 'initiated';
    case PENDING   = 'pending';
    case SUCCESS   = 'success';
    case FAILED    = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED   = 'expired';
    case REVERSED  = 'reversed';

    public function label(): string
    {
        return match ($this) {
            self::REQUIRES_FULFILLMENT             => 'Requires fulfillment',
            self::REQUIRES_CONFIRMATION            => 'Requires confirmation',
            self::REQUIRES_PAYOUT_METHOD_DATA      => 'Requires payout method data',
            self::REQUIRES_VENDOR_ACCOUNT_CREATION => 'Requires vendor account creation',
            self::INITIATED                        => 'Initiated',
            self::PENDING                          => 'Pending',
            self::SUCCESS                          => 'Success',
            self::FAILED                           => 'Failed',
            self::CANCELLED                        => 'Cancelled',
            self::EXPIRED                          => 'Expired',
            self::REVERSED                         => 'Reversed',
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::SUCCESS, self::FAILED, self::CANCELLED, self::EXPIRED, self::REVERSED => true,
            default => false,
        };
    }
}
