<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * Top-level payment method category. Per the connector-blob convention,
 * the corresponding subtype (`payment_method_subtype` — credit, debit,
 * google_pay, apple_pay, etc.) varies per connector and stays as a string;
 * only the broad category is enumerated here.
 *
 * @postman             Payments / Payments - Create (payment_method)
 * @hyperswitch-source  2026-04-30
 */
enum PaymentMethod: string implements LabelledEnum
{
    case CARD              = 'card';
    case CARD_REDIRECT     = 'card_redirect';
    case PAY_LATER         = 'pay_later';
    case WALLET            = 'wallet';
    case BANK_REDIRECT     = 'bank_redirect';
    case BANK_TRANSFER     = 'bank_transfer';
    case BANK_DEBIT        = 'bank_debit';
    case CRYPTO            = 'crypto';
    case REWARD            = 'reward';
    case REAL_TIME_PAYMENT = 'real_time_payment';
    case UPI               = 'upi';
    case VOUCHER           = 'voucher';
    case GIFT_CARD         = 'gift_card';
    case OPEN_BANKING      = 'open_banking';
    case MOBILE_PAYMENT    = 'mobile_payment';
    case NETWORK_TOKEN     = 'network_token';

    public function label(): string
    {
        return match ($this) {
            self::CARD              => 'Card',
            self::CARD_REDIRECT     => 'Card (redirect)',
            self::PAY_LATER         => 'Pay later',
            self::WALLET            => 'Wallet',
            self::BANK_REDIRECT     => 'Bank redirect',
            self::BANK_TRANSFER     => 'Bank transfer',
            self::BANK_DEBIT        => 'Bank debit',
            self::CRYPTO            => 'Crypto',
            self::REWARD            => 'Reward',
            self::REAL_TIME_PAYMENT => 'Real-time payment',
            self::UPI               => 'UPI',
            self::VOUCHER           => 'Voucher',
            self::GIFT_CARD         => 'Gift card',
            self::OPEN_BANKING      => 'Open banking',
            self::MOBILE_PAYMENT    => 'Mobile payment',
            self::NETWORK_TOKEN     => 'Network token',
        };
    }
}
