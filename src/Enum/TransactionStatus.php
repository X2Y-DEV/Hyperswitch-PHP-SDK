<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * EMV 3DS transaction status returned by external authentication.
 *
 * v1 spec schema: TransactionStatus
 *
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
enum TransactionStatus: string implements LabelledEnum
{
    case SUCCESS                        = 'Y';
    case FAILED                         = 'N';
    case UNAVAILABLE                    = 'U';
    case ATTEMPTED                      = 'A';
    case REJECTED                       = 'R';
    case CHALLENGE_REQUIRED             = 'C';
    case CHALLENGE_REQUIRED_DECOUPLED   = 'D';
    case INFORMATIONAL                  = 'I';

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS                      => 'Authenticated',
            self::FAILED                       => 'Not authenticated',
            self::UNAVAILABLE                  => 'Authentication unavailable',
            self::ATTEMPTED                    => 'Attempted (proof of attempt)',
            self::REJECTED                     => 'Rejected',
            self::CHALLENGE_REQUIRED           => 'Challenge required',
            self::CHALLENGE_REQUIRED_DECOUPLED => 'Challenge required (decoupled)',
            self::INFORMATIONAL                => 'Informational only',
        };
    }
}
