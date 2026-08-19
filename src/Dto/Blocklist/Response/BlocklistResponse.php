<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Blocklist\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * @openapi             "Block a Fingerprint"
 * @endpoint            POST /blocklist
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class BlocklistResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $fingerprintId,
        public readonly string $dataKind,
        public readonly ?\DateTimeImmutable $createdAt = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['fingerprint_id', 'data_kind', 'created_at'];

        return new self(
            fingerprintId: (string) $data['fingerprint_id'],
            dataKind:      (string) ($data['data_kind'] ?? ''),
            createdAt:     DateTimeParser::parse($data['created_at'] ?? null),
            extras:        array_diff_key($data, array_flip($known)),
        );
    }
}
