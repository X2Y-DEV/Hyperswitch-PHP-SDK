<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\Currency;

/**
 * Body for POST /payouts/list — filter payouts using specific constraints.
 * (v1 spec schema: PayoutListFilterConstraints; extends TimeRange.)
 *
 * @openapi             "Filter payouts using specific constraints"
 * @endpoint            POST /payouts/list
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class FilterPayoutsRequest extends AbstractRequestDto
{
    /**
     * @param list<string>|null    $connector
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly Currency $currency,
        public readonly string $entityType,
        public readonly ?\DateTimeImmutable $startTime = null,
        public readonly ?\DateTimeImmutable $endTime = null,
        public readonly ?string $payoutId = null,
        public readonly ?string $merchantOrderReferenceId = null,
        public readonly ?string $profileId = null,
        public readonly ?string $customerId = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?array $connector = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'currency'                    => $this->currency->value,
            'entity_type'                 => $this->entityType,
            'start_time'                  => $this->startTime?->format(\DateTimeInterface::ATOM),
            'end_time'                    => $this->endTime?->format(\DateTimeInterface::ATOM),
            'payout_id'                   => $this->payoutId,
            'merchant_order_reference_id' => $this->merchantOrderReferenceId,
            'profile_id'                  => $this->profileId,
            'customer_id'                 => $this->customerId,
            'limit'                       => $this->limit,
            'offset'                      => $this->offset,
            'connector'                   => $this->connector,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
