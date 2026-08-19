<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Execute 3DS Decision Rule"
 * @endpoint            POST /three_ds_decision/execute
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ThreeDsDecisionExecuteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $decision,
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
            decision: (string) $data['decision'],
            extras:   array_diff_key($data, array_flip(['decision'])),
        );
    }
}
