<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * @postman             Payments / Payments - Create (capture_method)
 * @hyperswitch-source  2026-04-30
 */
enum CaptureMethod: string implements LabelledEnum
{
    case AUTOMATIC       = 'automatic';
    case MANUAL          = 'manual';
    case MANUAL_MULTIPLE = 'manual_multiple';
    case SCHEDULED       = 'scheduled';

    public function label(): string
    {
        return match ($this) {
            self::AUTOMATIC       => 'Automatic',
            self::MANUAL          => 'Manual',
            self::MANUAL_MULTIPLE => 'Manual (multiple captures)',
            self::SCHEDULED       => 'Scheduled',
        };
    }
}
