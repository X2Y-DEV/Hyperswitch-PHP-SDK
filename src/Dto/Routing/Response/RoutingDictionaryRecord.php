<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Routing\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * A routing algorithm dictionary record — returned by most routing mutations.
 *
 * @openapi             tag: Routing (RoutingDictionaryRecord)
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class RoutingDictionaryRecord extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $id,
        public readonly string $profileId,
        public readonly string $name,
        public readonly string $kind,
        public readonly string $description,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $modifiedAt = null,
        public readonly ?string $algorithmFor = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'id', 'profile_id', 'name', 'kind', 'description', 'created_at',
            'modified_at', 'algorithm_for', 'decision_engine_routing_id',
        ];

        return new self(
            id:           (string) $data['id'],
            profileId:    (string) ($data['profile_id'] ?? ''),
            name:         (string) ($data['name'] ?? ''),
            kind:         (string) ($data['kind'] ?? ''),
            description:  (string) ($data['description'] ?? ''),
            createdAt:    DateTimeParser::parse($data['created_at'] ?? null),
            modifiedAt:   DateTimeParser::parse($data['modified_at'] ?? null),
            algorithmFor: isset($data['algorithm_for']) ? (string) $data['algorithm_for'] : null,
            extras:       array_diff_key($data, array_flip($known)),
        );
    }
}
