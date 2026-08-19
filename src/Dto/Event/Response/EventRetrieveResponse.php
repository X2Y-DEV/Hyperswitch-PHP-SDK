<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Event\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * A single event (returned by retry). Broad connector-dependent shape — the
 * event_id is surfaced; the rest is preserved in `extras`.
 *
 * @openapi             "Manually retry the delivery of an Event"
 * @endpoint            POST /events/{id}/{id}/retry
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class EventRetrieveResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $eventId = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            eventId: isset($data['event_id']) ? (string) $data['event_id'] : null,
            extras:  array_diff_key($data, array_flip(['event_id'])),
        );
    }
}
