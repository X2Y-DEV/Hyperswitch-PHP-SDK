<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;

/**
 * @openapi             "Create Post Session Tokens for a Payment"
 * @endpoint            POST /payments/{id}/post_session_tokens
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PostSessionTokensRequest extends AbstractRequestDto
{
    /**
     * @param string               $paymentMethodType v1 spec `PaymentMethodType` (e.g. google_pay, apple_pay).
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly PaymentMethod $paymentMethod,
        public readonly string $paymentMethodType,
        public readonly ?string $clientSecret = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_method'      => $this->paymentMethod->value,
            'payment_method_type' => $this->paymentMethodType,
            'client_secret'       => $this->clientSecret,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
