<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Routing\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Body for routing rule evaluation (POST /routing/evaluate and
 * /routing/rule/evaluate). The parameters vary per rule engine, so the body is
 * a passthrough blob.
 *
 * @openapi             "Evaluate routing rules"
 * @endpoint            POST /routing/rule/evaluate
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class RoutingEvaluateRequest extends AbstractRequestDto
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
