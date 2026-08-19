<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Gsm\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Create Gsm Rule"
 * @endpoint            POST /gsm
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $connector,
        public readonly string $flow,
        public readonly string $subFlow,
        public readonly string $code,
        public readonly ?string $message = null,
        public readonly ?string $status = null,
        public readonly ?string $decision = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['connector', 'flow', 'sub_flow', 'code', 'message', 'status', 'decision'];

        return new self(
            connector: (string) $data['connector'],
            flow:      (string) ($data['flow'] ?? ''),
            subFlow:   (string) ($data['sub_flow'] ?? ''),
            code:      (string) ($data['code'] ?? ''),
            message:   isset($data['message']) ? (string) $data['message'] : null,
            status:    isset($data['status']) ? (string) $data['status'] : null,
            decision:  isset($data['decision']) ? (string) $data['decision'] : null,
            extras:    array_diff_key($data, array_flip($known)),
        );
    }
}
