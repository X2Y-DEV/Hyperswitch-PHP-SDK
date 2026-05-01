<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * Response from /v1/payment-method-sessions create / retrieve / confirm /
 * update / token-lookup.
 *
 * The deeper session payload (linked payment method, redirect data, etc.)
 * varies per flow; preserved in `extras`.
 *
 * @postman             Payment Method Sessions / Payment Methods Session - Retrieve
 * @endpoint            GET /v1/payment-method-sessions/{id}
 * @hyperswitch-source  2026-04-30
 */
final class SessionResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $customerId = null,
        public readonly ?string $clientSecret = null,
        public readonly ?string $status = null,
        public readonly ?string $paymentMethodId = null,
        public readonly ?\DateTimeImmutable $expiresAt = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'id', 'session_id', 'customer_id', 'client_secret', 'status',
            'payment_method_id', 'expires_at', 'created_at',
        ];

        return new self(
            id:              (string) ($data['id'] ?? $data['session_id'] ?? ''),
            customerId:      isset($data['customer_id'])       ? (string) $data['customer_id']       : null,
            clientSecret:    isset($data['client_secret'])     ? (string) $data['client_secret']     : null,
            status:          isset($data['status'])            ? (string) $data['status']            : null,
            paymentMethodId: isset($data['payment_method_id']) ? (string) $data['payment_method_id'] : null,
            expiresAt:       DateTimeParser::parse($data['expires_at'] ?? null),
            createdAt:       DateTimeParser::parse($data['created_at'] ?? null),
            extras:          array_diff_key($data, array_flip($known)),
        );
    }
}
