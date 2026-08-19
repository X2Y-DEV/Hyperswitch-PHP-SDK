<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Response from POST /payments/session_tokens.
 *
 * `sessionTokens` is a heterogeneous list of connector-specific token blobs
 * (Apple Pay merchant-validation payload, Google Pay token data, etc.).
 * Each entry's shape varies per wallet, so per the connector-blob convention
 * the items stay typed as `array<string, mixed>`.
 *
 * @endpoint            POST /payments/session_tokens
 * @hyperswitch-source  2026-04-30
 */
final class SessionTokensResponse extends AbstractResponseDto
{
    /**
     * @param list<array<string, mixed>> $sessionTokens
     * @param array<string, mixed>       $extras
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly string $clientSecret,
        public readonly array $sessionTokens = [],
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['payment_id', 'client_secret', 'session_token'];
        $tokens = [];
        $rawTokens = $data['session_token'] ?? [];
        if (is_array($rawTokens)) {
            foreach ($rawTokens as $tok) {
                if (is_array($tok)) {
                    /** @var array<string, mixed> $tok */
                    $tokens[] = $tok;
                }
            }
        }

        return new self(
            paymentId:     (string) $data['payment_id'],
            clientSecret:  (string) $data['client_secret'],
            sessionTokens: $tokens,
            extras:        array_diff_key($data, array_flip($known)),
        );
    }
}
