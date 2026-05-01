<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Refund\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\RefundStatus;

/**
 * Filter refunds by payment, time range, status, etc. Postman ships this
 * as `POST /refunds/list` with an empty default body — every field is optional.
 *
 * @postman             Refunds / Refunds - List
 * @endpoint            POST /refunds/list
 * @hyperswitch-source  2026-04-30
 */
final class ListRefundsRequest extends AbstractRequestDto
{
    /**
     * @param list<RefundStatus>|null $refundStatus
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?string $paymentId = null,
        public readonly ?string $profileId = null,
        public readonly ?\DateTimeImmutable $startTime = null,
        public readonly ?\DateTimeImmutable $endTime = null,
        public readonly ?array $refundStatus = null,
        public readonly ?int $amount = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'limit'         => $this->limit,
            'offset'        => $this->offset,
            'payment_id'    => $this->paymentId,
            'profile_id'    => $this->profileId,
            'start_time'    => $this->startTime?->format(\DateTimeInterface::ATOM),
            'end_time'      => $this->endTime?->format(\DateTimeInterface::ATOM),
            'refund_status' => $this->refundStatus !== null
                ? array_map(static fn (RefundStatus $s): string => $s->value, $this->refundStatus)
                : null,
            'amount'        => $this->amount,
        ]);
    }
}
