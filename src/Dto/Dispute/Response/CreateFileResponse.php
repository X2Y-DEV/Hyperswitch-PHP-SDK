<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Attach Evidence to Dispute"
 * @endpoint            PUT /disputes/evidence
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CreateFileResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $fileId,
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
            fileId: (string) $data['file_id'],
            extras: array_diff_key($data, array_flip(['file_id'])),
        );
    }
}
