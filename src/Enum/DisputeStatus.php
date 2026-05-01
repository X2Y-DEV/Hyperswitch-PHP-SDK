<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Disputes / Disputes - Retrieve (dispute_status)
 * @hyperswitch-source  2026-04-30
 */
enum DisputeStatus: string implements LabelledEnum
{
    case DISPUTE_OPENED          = 'dispute_opened';
    case DISPUTE_EXPIRED         = 'dispute_expired';
    case DISPUTE_ACCEPTED        = 'dispute_accepted';
    case DISPUTE_CANCELLED       = 'dispute_cancelled';
    case DISPUTE_CHALLENGED      = 'dispute_challenged';
    case DISPUTE_WON             = 'dispute_won';
    case DISPUTE_LOST            = 'dispute_lost';

    public function label(): string
    {
        return match ($this) {
            self::DISPUTE_OPENED     => 'Opened',
            self::DISPUTE_EXPIRED    => 'Expired',
            self::DISPUTE_ACCEPTED   => 'Accepted',
            self::DISPUTE_CANCELLED  => 'Cancelled',
            self::DISPUTE_CHALLENGED => 'Challenged',
            self::DISPUTE_WON        => 'Won',
            self::DISPUTE_LOST       => 'Lost',
        };
    }
}
