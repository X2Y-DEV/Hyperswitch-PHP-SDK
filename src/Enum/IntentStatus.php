<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * Payment intent (top-level payment) status as returned by Hyperswitch.
 *
 * @postman             Payments / Payments - Retrieve (status)
 * @hyperswitch-source  2026-04-30
 */
enum IntentStatus: string implements LabelledEnum
{
    case REQUIRES_PAYMENT_METHOD          = 'requires_payment_method';
    case REQUIRES_CONFIRMATION            = 'requires_confirmation';
    case REQUIRES_CUSTOMER_ACTION         = 'requires_customer_action';
    case REQUIRES_MERCHANT_ACTION         = 'requires_merchant_action';
    case REQUIRES_CAPTURE                 = 'requires_capture';
    case PROCESSING                       = 'processing';
    case SUCCEEDED                        = 'succeeded';
    case PARTIALLY_CAPTURED               = 'partially_captured';
    case PARTIALLY_CAPTURED_AND_CAPTURABLE = 'partially_captured_and_capturable';
    case CANCELLED                        = 'cancelled';
    case FAILED                           = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::REQUIRES_PAYMENT_METHOD           => 'Requires payment method',
            self::REQUIRES_CONFIRMATION             => 'Requires confirmation',
            self::REQUIRES_CUSTOMER_ACTION          => 'Requires customer action',
            self::REQUIRES_MERCHANT_ACTION          => 'Requires merchant action',
            self::REQUIRES_CAPTURE                  => 'Requires capture',
            self::PROCESSING                        => 'Processing',
            self::SUCCEEDED                         => 'Succeeded',
            self::PARTIALLY_CAPTURED                => 'Partially captured',
            self::PARTIALLY_CAPTURED_AND_CAPTURABLE => 'Partially captured (more capturable)',
            self::CANCELLED                         => 'Cancelled',
            self::FAILED                            => 'Failed',
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::SUCCEEDED, self::PARTIALLY_CAPTURED, self::CANCELLED, self::FAILED => true,
            default => false,
        };
    }
}
