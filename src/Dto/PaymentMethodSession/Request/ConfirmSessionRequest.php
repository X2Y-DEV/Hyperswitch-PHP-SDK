<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;

/**
 * Confirm a payment-method session by attaching a tokenised instrument.
 *
 * @postman             Payment Method Sessions / Payment Methods Session - Confirm
 * @endpoint            POST /v1/payment-method-sessions/{id}/confirm
 * @hyperswitch-source  2026-04-30
 */
final class ConfirmSessionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $paymentMethodData  e.g. ['card' => ['card_number' => '4242…', …]]
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly array $paymentMethodData,
        public readonly ?PaymentMethod $paymentMethodType = null,
        public readonly ?string $paymentMethodSubtype = null,
        public readonly ?string $returnUrl = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_method_data'    => $this->paymentMethodData,
            'payment_method_type'    => $this->paymentMethodType?->value,
            'payment_method_subtype' => $this->paymentMethodSubtype,
            'return_url'             => $this->returnUrl,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
