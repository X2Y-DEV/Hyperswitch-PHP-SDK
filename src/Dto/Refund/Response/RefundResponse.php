<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Refund\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\RefundStatus;
use X2Y\HyperswitchClient\Enum\RefundType;

/**
 * Response from /refunds create / retrieve / update.
 *
 * Stub set of fields — extend as Hyperswitch's response shape evolves. Unknown
 * fields are preserved in `extras()`.
 *
 * @postman             Refunds / Refunds - Retrieve
 * @endpoint            GET /refunds/{id}
 * @hyperswitch-source  2026-04-30
 */
final class RefundResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $refundId,
        public readonly string $paymentId,
        public readonly RefundStatus $status,
        public readonly int $amount,
        public readonly Currency $currency,
        public readonly ?RefundType $refundType = null,
        public readonly ?string $reason = null,
        public readonly ?string $errorMessage = null,
        public readonly ?string $errorCode = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
        public readonly ?string $connector = null,
        public readonly ?string $profileId = null,
        public readonly ?string $merchantConnectorId = null,
        public readonly ?array $metadata = null,
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
            'refund_id', 'payment_id', 'status', 'amount', 'currency',
            'refund_type', 'reason', 'error_message', 'error_code',
            'created_at', 'updated_at', 'connector', 'profile_id',
            'merchant_connector_id', 'metadata',
        ];

        $metadata = $data['metadata'] ?? null;

        return new self(
            refundId:            (string) $data['refund_id'],
            paymentId:           (string) $data['payment_id'],
            status:              RefundStatus::from((string) $data['status']),
            amount:              (int) $data['amount'],
            currency:            Currency::from((string) $data['currency']),
            refundType:          isset($data['refund_type'])
                ? RefundType::from((string) $data['refund_type'])
                : null,
            reason:              isset($data['reason'])        ? (string) $data['reason']        : null,
            errorMessage:        isset($data['error_message']) ? (string) $data['error_message'] : null,
            errorCode:           isset($data['error_code'])    ? (string) $data['error_code']    : null,
            createdAt:           isset($data['created_at'])    ? new \DateTimeImmutable((string) $data['created_at']) : null,
            updatedAt:           isset($data['updated_at'])    ? new \DateTimeImmutable((string) $data['updated_at']) : null,
            connector:           isset($data['connector'])     ? (string) $data['connector']     : null,
            profileId:           isset($data['profile_id'])    ? (string) $data['profile_id']    : null,
            merchantConnectorId: isset($data['merchant_connector_id']) ? (string) $data['merchant_connector_id'] : null,
            metadata:            is_array($metadata) ? $metadata : null,
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
