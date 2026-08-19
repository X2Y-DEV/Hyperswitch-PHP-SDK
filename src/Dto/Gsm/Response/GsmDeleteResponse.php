<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Gsm\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Delete Gsm Rule"
 * @endpoint            POST /gsm/delete
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly bool $gsmRuleDelete,
        public readonly string $connector,
        public readonly string $flow,
        public readonly string $subFlow,
        public readonly string $code,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['gsm_rule_delete', 'connector', 'flow', 'sub_flow', 'code'];

        return new self(
            gsmRuleDelete: (bool) ($data['gsm_rule_delete'] ?? false),
            connector:     (string) ($data['connector'] ?? ''),
            flow:          (string) ($data['flow'] ?? ''),
            subFlow:       (string) ($data['sub_flow'] ?? ''),
            code:          (string) ($data['code'] ?? ''),
            extras:        array_diff_key($data, array_flip($known)),
        );
    }
}
