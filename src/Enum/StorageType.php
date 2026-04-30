<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * How long a tokenised payment method is retained.
 *
 * @postman             Payment Methods - Modular Payment / Payment Methods - Create (storage_type)
 * @hyperswitch-source  2026-04-30
 */
enum StorageType: string implements LabelledEnum
{
    case VOLATILE   = 'volatile';
    case PERSISTENT = 'persistent';

    public function label(): string
    {
        return match ($this) {
            self::VOLATILE   => 'Volatile (single-use)',
            self::PERSISTENT => 'Persistent (saved for reuse)',
        };
    }
}
