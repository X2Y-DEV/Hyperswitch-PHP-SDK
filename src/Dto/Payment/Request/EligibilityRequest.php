<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;

/**
 * @openapi             "Submit Eligibility data for a Payment"
 * @endpoint            POST /payments/{id}/eligibility
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class EligibilityRequest extends AbstractRequestDto
{
    /**
     * @param string|null               $paymentMethodSubtype v1 spec `PaymentMethodType` (e.g. google_pay, apple_pay).
     * @param array<string, mixed>|null $paymentMethodData    Connector-specific payment-method payload.
     * @param array<string, mixed>|null $browserInfo          Browser fingerprint for 3DS.
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $clientSecret,
        public readonly PaymentMethod $paymentMethodType,
        public readonly string $paymentToken,
        public readonly ?string $paymentMethodSubtype = null,
        public readonly ?array $paymentMethodData = null,
        public readonly ?array $browserInfo = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'client_secret'          => $this->clientSecret,
            'payment_method_type'    => $this->paymentMethodType->value,
            'payment_token'          => $this->paymentToken,
            'payment_method_subtype' => $this->paymentMethodSubtype,
            'payment_method_data'    => $this->paymentMethodData,
            'browser_info'           => $this->browserInfo,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
