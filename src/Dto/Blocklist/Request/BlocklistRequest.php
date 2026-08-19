<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Blocklist\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Block/unblock body (POST and DELETE /blocklist). `type` selects the kind
 * (card_bin / fingerprint / extended_card_bin); `data` is the value.
 *
 * @openapi             "Block a Fingerprint"
 * @endpoint            POST /blocklist
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class BlocklistRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $data = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'type' => $this->type,
            'data' => $this->data,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
