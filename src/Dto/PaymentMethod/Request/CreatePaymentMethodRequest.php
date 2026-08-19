<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethod\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\BillingDetails;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;
use X2Y\HyperSwitchClient\Enum\StorageType;

/**
 * Tokenize and persist a payment method against a customer.
 *
 * `paymentMethodData` is the connector-specific instrument payload — card
 * details, wallet token, bank account, etc. — and stays loose per the
 * connector-blob convention.
 *
 * @endpoint            POST /v1/payment-methods
 * @hyperswitch-source  2026-04-30
 */
final class CreatePaymentMethodRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>      $paymentMethodData    e.g. ['card' => ['card_number' => '4242…', …]]
     * @param array<string, mixed>|null $pspTokenization
     * @param array<string, mixed>|null $networkTokenization
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly PaymentMethod $paymentMethodType,
        public readonly array $paymentMethodData,
        public readonly ?string $paymentMethodSubtype = null,
        public readonly ?StorageType $storageType = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?array $pspTokenization = null,
        public readonly ?array $networkTokenization = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'customer_id'           => $this->customerId,
            'payment_method_type'   => $this->paymentMethodType->value,
            'payment_method_subtype'=> $this->paymentMethodSubtype,
            'payment_method_data'   => $this->paymentMethodData,
            'storage_type'          => $this->storageType?->value,
            'billing'               => $this->billing?->toArray(),
            'psp_tokenization'      => $this->pspTokenization,
            'network_tokenization'  => $this->networkTokenization,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
