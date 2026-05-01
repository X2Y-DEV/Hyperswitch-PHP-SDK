<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\Currency;
use X2Y\HyperSwitchClient\Enum\DisputeStage;
use X2Y\HyperSwitchClient\Enum\DisputeStatus;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * Response from /disputes retrieve / list (item) / accept / submit-evidence.
 *
 * @postman             Disputes / Disputes - Retrieve
 * @endpoint            GET /disputes/{id}
 * @hyperswitch-source  2026-04-30
 */
final class DisputeResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $disputeId,
        public readonly DisputeStatus $disputeStatus,
        public readonly ?DisputeStage $disputeStage = null,
        public readonly ?string $paymentId = null,
        public readonly ?string $attemptId = null,
        public readonly ?int $amount = null,
        public readonly ?Currency $currency = null,
        public readonly ?string $connectorStatus = null,
        public readonly ?string $connectorDisputeId = null,
        public readonly ?string $connectorReason = null,
        public readonly ?string $connectorReasonCode = null,
        public readonly ?\DateTimeImmutable $challengeRequiredBy = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $updatedAt = null,
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
            'dispute_id', 'dispute_status', 'dispute_stage',
            'payment_id', 'attempt_id', 'amount', 'currency',
            'connector_status', 'connector_dispute_id',
            'connector_reason', 'connector_reason_code',
            'challenge_required_by', 'created_at', 'updated_at',
        ];

        return new self(
            disputeId:        (string) $data['dispute_id'],
            disputeStatus:    DisputeStatus::from((string) $data['dispute_status']),
            disputeStage:     isset($data['dispute_stage'])
                ? DisputeStage::from((string) $data['dispute_stage'])
                : null,
            paymentId:        isset($data['payment_id'])  ? (string) $data['payment_id']  : null,
            attemptId:        isset($data['attempt_id'])  ? (string) $data['attempt_id']  : null,
            amount:           isset($data['amount'])      ? (int)    $data['amount']      : null,
            currency:         isset($data['currency'])
                ? Currency::from((string) $data['currency'])
                : null,
            connectorStatus:     isset($data['connector_status'])      ? (string) $data['connector_status']      : null,
            connectorDisputeId:  isset($data['connector_dispute_id'])  ? (string) $data['connector_dispute_id']  : null,
            connectorReason:     isset($data['connector_reason'])      ? (string) $data['connector_reason']      : null,
            connectorReasonCode: isset($data['connector_reason_code']) ? (string) $data['connector_reason_code'] : null,
            challengeRequiredBy: DateTimeParser::parse($data['challenge_required_by'] ?? null),
            createdAt:           DateTimeParser::parse($data['created_at'] ?? null),
            updatedAt:           DateTimeParser::parse($data['updated_at'] ?? null),
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
