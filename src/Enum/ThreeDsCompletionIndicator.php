<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * Indicates whether the 3DS method data was successfully completed.
 *
 * v1 spec schema: ThreeDsCompletionIndicator
 *
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
enum ThreeDsCompletionIndicator: string implements LabelledEnum
{
    case COMPLETED     = 'Y';
    case NOT_COMPLETED = 'N';
    case UNAVAILABLE   = 'U';

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED     => 'Completed',
            self::NOT_COMPLETED => 'Not completed',
            self::UNAVAILABLE   => 'Unavailable',
        };
    }
}
