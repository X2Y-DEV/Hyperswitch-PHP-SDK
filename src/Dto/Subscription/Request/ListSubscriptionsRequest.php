<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\SubscriptionStatus;

/**
 * Filters for listing subscriptions. Sent as query parameters on a GET
 * request — Resource passes `toArray()` as `queryParams`.
 *
 * @postman             Subscriptions / Subscription - List
 * @endpoint            GET /subscriptions/list
 * @hyperswitch-source  2026-04-30
 */
final class ListSubscriptionsRequest extends AbstractRequestDto
{
    /**
     * @param list<SubscriptionStatus>|null $status
     * @param array<string, mixed>          $extras
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?string $customerId = null,
        public readonly ?string $profileId = null,
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
            'status'      => $this->status !== null
                ? array_map(static fn (SubscriptionStatus $s): string => $s->value, $this->status)
                : null,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
