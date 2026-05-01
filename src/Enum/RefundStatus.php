<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * Status of a refund as returned by HyperSwitch.
 *
 * @postman             Refunds / Refunds - Retrieve (status)
 * @hyperswitch-source  2026-04-30
 */
enum RefundStatus: string implements LabelledEnum
{
    case SUCCEEDED            = 'succeeded';
    case FAILED               = 'failed';
    case PENDING              = 'pending';
    case REVIEW               = 'review';
    case MANUAL_REVIEW        = 'manual_review';

    public function label(): string
    {
        return match ($this) {
            self::SUCCEEDED     => 'Succeeded',
            self::FAILED        => 'Failed',
            self::PENDING       => 'Pending',
            self::REVIEW        => 'Under review',
            self::MANUAL_REVIEW => 'Under manual review',
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::SUCCEEDED, self::FAILED => true,
            default                       => false,
        };
    }
}
