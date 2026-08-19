<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Event\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Constraints for listing events (POST /events/{id} and /events/profile/list).
 *
 * @openapi             "List all Events associated with a Merchant Account or Profile"
 * @endpoint            POST /events/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class EventListConstraintsRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras Extra constraints (event_classes, event_types, recipient, created_after/before, …).
     */
    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?string $eventId = null,
        public readonly ?string $objectId = null,
        public readonly ?string $profileId = null,
        public readonly ?bool $isDelivered = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'limit'        => $this->limit,
            'offset'       => $this->offset,
            'event_id'     => $this->eventId,
            'object_id'    => $this->objectId,
            'profile_id'   => $this->profileId,
            'is_delivered' => $this->isDelivered,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
