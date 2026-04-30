<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * Acknowledgement status reported when updating a saved payment method
 * after a CIT — typically following an external authentication challenge.
 *
 * @postman             Payment Methods - Modular Payment / Payment Methods - Update (acknowledgement_status)
 * @hyperswitch-source  2026-04-30
 */
enum PaymentMethodAckStatus: string implements LabelledEnum
{
    case AUTHENTICATED = 'authenticated';
    case FAILED        = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::AUTHENTICATED => 'Authenticated',
            self::FAILED        => 'Failed',
        };
    }
}
