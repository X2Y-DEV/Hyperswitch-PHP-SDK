<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Filters for the subscription plans catalog. Sent as query parameters.
 *
 * @postman             Subscriptions / Subscription - Plans
 * @endpoint            GET /subscriptions/plans
 * @hyperswitch-source  2026-04-30
 */
final class ListPlansRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'limit'  => $this->limit,
            'offset' => $this->offset,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
