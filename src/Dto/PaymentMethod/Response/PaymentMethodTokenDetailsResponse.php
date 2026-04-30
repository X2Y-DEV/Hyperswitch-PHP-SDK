<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\PaymentMethod\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\PaymentMethod;

/**
 * Response from `GET /v1/payment-methods/token/{token}/details` — looks up
 * a payment method by its temporary client-side token and returns the
 * underlying instrument metadata.
 *
 * @postman             Payment Methods - Modular Payment / Payment Method - Get Payment Method Token Data
 * @endpoint            GET /v1/payment-methods/token/{token}/details
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodTokenDetailsResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $paymentMethodId = null,
        public readonly ?string $customerId = null,
        public readonly ?PaymentMethod $paymentMethodType = null,
        public readonly ?string $paymentMethodSubtype = null,
        public readonly ?string $status = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'payment_method_id', 'customer_id', 'payment_method_type',
            'payment_method_subtype', 'status',
        ];

        return new self(
            paymentMethodId: isset($data['payment_method_id']) ? (string) $data['payment_method_id'] : null,
            customerId:      isset($data['customer_id'])       ? (string) $data['customer_id']       : null,
            paymentMethodType: isset($data['payment_method_type'])
                ? PaymentMethod::from((string) $data['payment_method_type'])
                : null,
            paymentMethodSubtype: isset($data['payment_method_subtype'])
                ? (string) $data['payment_method_subtype']
                : null,
            status:          isset($data['status']) ? (string) $data['status'] : null,
            extras:          array_diff_key($data, array_flip($known)),
        );
    }
}
