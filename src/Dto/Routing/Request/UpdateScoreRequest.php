<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Routing\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Body for POST /routing/feedback (update gateway scores). Passthrough blob.
 *
 * @openapi             "Update gateway scores"
 * @endpoint            POST /routing/feedback
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class UpdateScoreRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        return $this->mergeExtras([], $this->extras);
    }
}
