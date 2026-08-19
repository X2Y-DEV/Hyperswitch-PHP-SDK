<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Poll\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Retrieve Poll Status"
 * @endpoint            GET /poll/status/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PollResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $pollId,
        public readonly string $status,
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
            pollId: (string) $data['poll_id'],
            status: (string) $data['status'],
            extras: array_diff_key($data, array_flip(['poll_id', 'status'])),
        );
    }
}
