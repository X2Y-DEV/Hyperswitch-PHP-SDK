<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Gsm\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Create a GSM (Global Status Mapping) rule.
 *
 * @openapi             "Create Gsm Rule"
 * @endpoint            POST /gsm
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmCreateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras Optional fields (description, feature, feature_data, unified_code, …).
     */
    public function __construct(
        public readonly string $connector,
        public readonly string $flow,
        public readonly string $subFlow,
        public readonly string $code,
        public readonly string $message,
        public readonly string $status,
        public readonly string $decision,
        public readonly bool $stepUpPossible,
        public readonly bool $clearPanPossible,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = [
            'connector'          => $this->connector,
            'flow'               => $this->flow,
            'sub_flow'           => $this->subFlow,
            'code'               => $this->code,
            'message'            => $this->message,
            'status'             => $this->status,
            'decision'           => $this->decision,
            'step_up_possible'   => $this->stepUpPossible,
            'clear_pan_possible' => $this->clearPanPossible,
        ];
        return $this->mergeExtras($typed, $this->extras);
    }
}
