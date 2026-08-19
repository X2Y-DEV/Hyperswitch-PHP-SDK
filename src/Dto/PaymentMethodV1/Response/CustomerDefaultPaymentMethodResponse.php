<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Set the Payment Method as Default"
 * @endpoint            POST /{id}/payment_methods/{id}/default
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CustomerDefaultPaymentMethodResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly string $paymentMethod,
        public readonly ?string $defaultPaymentMethodId = null,
        public readonly ?string $paymentMethodType = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['customer_id', 'payment_method', 'default_payment_method_id', 'payment_method_type'];

        return new self(
            customerId:             (string) $data['customer_id'],
            paymentMethod:          (string) $data['payment_method'],
            defaultPaymentMethodId: isset($data['default_payment_method_id']) ? (string) $data['default_payment_method_id'] : null,
            paymentMethodType:      isset($data['payment_method_type']) ? (string) $data['payment_method_type'] : null,
            extras:                 array_diff_key($data, array_flip($known)),
        );
    }
}
