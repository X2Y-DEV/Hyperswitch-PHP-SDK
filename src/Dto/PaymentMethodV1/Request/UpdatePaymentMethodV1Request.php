<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Update a payment method on the v1 /payment_methods surface.
 * (v1 spec schema: PaymentMethodUpdate.)
 *
 * @openapi             "Update a Payment method"
 * @endpoint            POST /payment_methods/{id}/update
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class UpdatePaymentMethodV1Request extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $card
     * @param array<string, mixed>|null $wallet
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?array $card = null,
        public readonly ?array $wallet = null,
        public readonly ?string $clientSecret = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'card'          => $this->card,
            'wallet'        => $this->wallet,
            'client_secret' => $this->clientSecret,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
