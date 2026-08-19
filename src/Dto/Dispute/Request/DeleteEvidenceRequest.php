<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Delete Evidence attached to a Dispute"
 * @endpoint            DELETE /disputes/evidence
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class DeleteEvidenceRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $disputeId,
        public readonly string $evidenceType,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'dispute_id'    => $this->disputeId,
            'evidence_type' => $this->evidenceType,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
