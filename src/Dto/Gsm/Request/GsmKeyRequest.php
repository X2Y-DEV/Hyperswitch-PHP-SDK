<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Gsm\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Identifier body for retrieve/delete of a GSM rule.
 *
 * @openapi             "Retrieve Gsm Rule"
 * @endpoint            POST /gsm/get
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmKeyRequest extends AbstractRequestDto
{
    public function __construct(
        public readonly string $connector,
        public readonly string $flow,
        public readonly string $subFlow,
        public readonly string $code,
        public readonly string $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'connector' => $this->connector,
            'flow'      => $this->flow,
            'sub_flow'  => $this->subFlow,
            'code'      => $this->code,
            'message'   => $this->message,
        ];
    }
}
