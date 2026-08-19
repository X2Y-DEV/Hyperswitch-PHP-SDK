<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Blocklist\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Toggle blocklist guard for a particular merchant"
 * @endpoint            POST /blocklist/toggle
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ToggleBlocklistResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $blocklistGuardStatus,
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
            blocklistGuardStatus: (string) $data['blocklist_guard_status'],
            extras:               array_diff_key($data, array_flip(['blocklist_guard_status'])),
        );
    }
}
