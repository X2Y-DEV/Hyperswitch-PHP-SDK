<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Mint connector-specific session tokens for client-side wallets
 * (Apple Pay, Google Pay, etc.) tied to a payment.
 *
 * @postman             Payments / Session Token
 * @endpoint            POST /payments/session_tokens
 * @hyperswitch-source  2026-04-30
 */
final class SessionTokensRequest extends AbstractRequestDto
{
    /**
     * @param list<string>          $wallets list of wallet identifiers (e.g. ["apple_pay","google_pay"])
     * @param array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly string $clientSecret,
        public readonly array $wallets = [],
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_id'    => $this->paymentId,
            'client_secret' => $this->clientSecret,
            'wallets'       => $this->wallets,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
