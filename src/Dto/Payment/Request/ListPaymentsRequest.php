<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payment\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Enum\IntentStatus;

/**
 * List payments with optional filters. Sent as query parameters on a GET
 * request — Resource passes `toArray()` as `queryParams` rather than body.
 *
 * @postman             Payments / Payment-List
 * @endpoint            GET /payments/list
 * @hyperswitch-source  2026-04-30
 */
final class ListPaymentsRequest extends AbstractRequestDto
{
    /**
     * @param list<IntentStatus>|null $status
     * @param array<string, mixed>    $extras
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?string $customerId = null,
        public readonly ?string $profileId = null,
        public readonly ?\DateTimeImmutable $startTime = null,
        public readonly ?\DateTimeImmutable $endTime = null,
        public readonly ?int $amount = null,
        public readonly ?array $status = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'limit'       => $this->limit,
            'offset'      => $this->offset,
            'customer_id' => $this->customerId,
            'profile_id'  => $this->profileId,
            'start_time'  => $this->startTime?->format(\DateTimeInterface::ATOM),
            'end_time'    => $this->endTime?->format(\DateTimeInterface::ATOM),
            'amount'      => $this->amount,
            'status'      => $this->status !== null
                ? array_map(static fn (IntentStatus $s): string => $s->value, $this->status)
                : null,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
