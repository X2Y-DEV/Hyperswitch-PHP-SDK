<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * Whether an external-authentication request originates from an app or a browser.
 *
 * v1 spec schema: DeviceChannel
 *
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
enum DeviceChannel: string implements LabelledEnum
{
    case APP     = 'APP';
    case BROWSER = 'BRW';

    public function label(): string
    {
        return match ($this) {
            self::APP     => 'App',
            self::BROWSER => 'Browser',
        };
    }
}
