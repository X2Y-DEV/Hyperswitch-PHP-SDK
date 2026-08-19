<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Attach evidence to a dispute.
 *
 * The v1 endpoint accepts `multipart/form-data` (a file plus form fields). This
 * JSON-oriented SDK cannot upload the binary itself; this DTO carries the form
 * fields (dispute_id, evidence_type, …) via `extras`, and consumers that need
 * to upload the file should send the multipart body through their own transport.
 *
 * @openapi             "Attach Evidence to Dispute"
 * @endpoint            PUT /disputes/evidence
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class AttachEvidenceRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $disputeId = null,
        public readonly ?string $evidenceType = null,
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
