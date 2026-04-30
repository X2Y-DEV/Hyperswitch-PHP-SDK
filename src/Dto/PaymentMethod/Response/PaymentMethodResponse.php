<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\PaymentMethod\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\PaymentMethod;
use X2Y\HyperswitchClient\Internal\DateTimeParser;

/**
 * Response from /v1/payment-methods create / retrieve / update / list (item).
 *
 * Card / wallet / bank-instrument details vary per payment method type and
 * are preserved in `extras`. Top-level scalars are typed.
 *
 * @postman             Payment Methods - Modular Payment / Payment Methods - Retrieve
 * @endpoint            GET /v1/payment-methods/{id}
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $paymentMethodId,
        public readonly ?string $customerId = null,
        public readonly ?PaymentMethod $paymentMethodType = null,
        public readonly ?string $paymentMethodSubtype = null,
        public readonly ?string $status = null,
        public readonly ?string $clientSecret = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $lastUsedAt = null,
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
            'payment_method_id', 'id', 'customer_id',
            'payment_method_type', 'payment_method_subtype',
            'status', 'client_secret', 'created_at', 'last_used_at',
        ];

        return new self(
            paymentMethodId:    (string) ($data['payment_method_id'] ?? $data['id'] ?? ''),
            customerId:         isset($data['customer_id']) ? (string) $data['customer_id'] : null,
            paymentMethodType:  isset($data['payment_method_type'])
                ? PaymentMethod::from((string) $data['payment_method_type'])
                : null,
            paymentMethodSubtype: isset($data['payment_method_subtype'])
                ? (string) $data['payment_method_subtype']
                : null,
            status:             isset($data['status'])        ? (string) $data['status']        : null,
            clientSecret:       isset($data['client_secret']) ? (string) $data['client_secret'] : null,
            createdAt:          DateTimeParser::parse($data['created_at']   ?? null),
            lastUsedAt:         DateTimeParser::parse($data['last_used_at'] ?? null),
            extras:             array_diff_key($data, array_flip($known)),
        );
    }
}
