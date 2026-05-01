<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * @postman             Disputes / Disputes - Retrieve (dispute_stage)
 * @hyperswitch-source  2026-04-30
 */
enum DisputeStage: string implements LabelledEnum
{
    case PRE_DISPUTE = 'pre_dispute';
    case DISPUTE     = 'dispute';
    case PRE_ARBITRATION = 'pre_arbitration';

    public function label(): string
    {
        return match ($this) {
            self::PRE_DISPUTE     => 'Pre-dispute (inquiry)',
            self::DISPUTE         => 'Dispute (chargeback)',
            self::PRE_ARBITRATION => 'Pre-arbitration',
        };
    }
}
