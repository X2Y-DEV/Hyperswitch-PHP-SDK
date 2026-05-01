<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Refund\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\RefundType;

/**
 * Initiate a full or partial refund against an existing payment.
 *
 * @postman             Refunds / Refunds - Create
 * @endpoint            POST /refunds
 * @hyperswitch-source  2026-04-30
 */
final class CreateRefundRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>|null $merchantConnectorDetails
     * @param array<string, mixed>|null $splitRefunds
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly ?int $amount = null,
        public readonly ?string $refundId = null,
        public readonly ?string $reason = null,
        public readonly ?RefundType $refundType = null,
        public readonly ?array $metadata = null,
        public readonly ?array $merchantConnectorDetails = null,
        public readonly ?array $splitRefunds = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'payment_id'                 => $this->paymentId,
            'amount'                     => $this->amount,
            'refund_id'                  => $this->refundId,
            'reason'                     => $this->reason,
            'refund_type'                => $this->refundType?->value,
            'metadata'                   => $this->metadata,
            'merchant_connector_details' => $this->merchantConnectorDetails,
            'split_refunds'              => $this->splitRefunds,
        ]);
    }
}
