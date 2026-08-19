<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Gsm\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Update a GSM rule — identified by (connector, flow, sub_flow, code, message);
 * updatable fields are passed via `extras`.
 *
 * @openapi             "Update Gsm Rule"
 * @endpoint            POST /gsm/update
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmUpdateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras Fields to update (status, decision, step_up_possible, …).
     */
    public function __construct(
        public readonly string $connector,
        public readonly string $flow,
        public readonly string $subFlow,
        public readonly string $code,
        public readonly string $message,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = [
            'connector' => $this->connector,
            'flow'      => $this->flow,
            'sub_flow'  => $this->subFlow,
            'code'      => $this->code,
            'message'   => $this->message,
        ];
        return $this->mergeExtras($typed, $this->extras);
    }
}
