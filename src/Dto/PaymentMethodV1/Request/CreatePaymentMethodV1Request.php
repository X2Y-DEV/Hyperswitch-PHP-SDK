<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;

/**
 * Create a payment method on the v1 /payment_methods surface.
 * (v1 spec schema: PaymentMethodCreate.)
 *
 * @openapi             "Create a Payment Method"
 * @endpoint            POST /payment_methods
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CreatePaymentMethodV1Request extends AbstractRequestDto
{
    /**
     * @param string|null               $paymentMethodType v1 spec `PaymentMethodType` (e.g. credit, debit, google_pay).
     * @param array<string, mixed>|null $card
     * @param array<string, mixed>|null $billing
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly PaymentMethod $paymentMethod,
        public readonly ?string $customerId = null,
        public readonly ?string $clientSecret = null,
        public readonly ?string $paymentMethodType = null,
        public readonly ?array $card = null,
        public readonly ?array $billing = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_method'      => $this->paymentMethod->value,
            'customer_id'         => $this->customerId,
            'client_secret'       => $this->clientSecret,
            'payment_method_type' => $this->paymentMethodType,
            'card'                => $this->card,
            'billing'             => $this->billing,
            'metadata'            => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
