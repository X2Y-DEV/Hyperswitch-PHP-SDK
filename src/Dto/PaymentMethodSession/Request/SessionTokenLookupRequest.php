<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Body for the token-lookup operation. The API calls this endpoint
 * `DELETE /v1/payment-method-sessions/{token}` with the token also in the
 * body — likely a token-revocation flow that returns the underlying PM id.
 * Modelled exactly as the API describes; unusual HTTP semantics are
 * HyperSwitch's, not ours to redesign.
 *
 * @endpoint            DELETE /v1/payment-method-sessions/{token}
 * @hyperswitch-source  2026-04-30
 */
final class SessionTokenLookupRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $paymentMethodToken,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_method_token' => $this->paymentMethodToken,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
