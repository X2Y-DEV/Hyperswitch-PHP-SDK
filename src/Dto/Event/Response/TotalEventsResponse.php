<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Event\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * A page of events plus the total count. Individual event items are kept as raw
 * arrays (their shape is broad and connector-dependent).
 *
 * @openapi             "List all Events associated with a Merchant Account or Profile"
 * @endpoint            POST /events/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class TotalEventsResponse extends AbstractResponseDto
{
    /**
     * @param list<array<string, mixed>> $events
     * @param array<string, mixed>       $extras
     */
    public function __construct(
        public readonly array $events,
        public readonly int $totalCount,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $events = [];
        foreach (($data['events'] ?? []) as $row) {
            if (is_array($row)) {
                /** @var array<string, mixed> $row */
                $events[] = $row;
            }
        }

        return new self(
            events:     $events,
            totalCount: (int) ($data['total_count'] ?? 0),
            extras:     array_diff_key($data, array_flip(['events', 'total_count'])),
        );
    }
}
