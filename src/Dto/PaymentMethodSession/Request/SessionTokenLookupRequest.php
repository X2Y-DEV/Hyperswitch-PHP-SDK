<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\PaymentMethodSession\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * Body for the token-lookup operation. Postman calls this endpoint
 * `DELETE /v1/payment-method-sessions/{token}` with the token also in the
 * body — likely a token-revocation flow that returns the underlying PM id.
 * Modelled exactly as Postman describes; unusual HTTP semantics are
 * Hyperswitch's, not ours to redesign.
 *
 * @postman             Payment Method Sessions / Get PM ID from Payment Method token Copy
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
