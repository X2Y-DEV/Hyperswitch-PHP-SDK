<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Update Metadata for a Payment"
 * @endpoint            POST /payments/{id}/update_metadata
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class UpdateMetadataPaymentRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>      $metadata        Arbitrary merchant metadata (required by the endpoint).
     * @param array<string, mixed>|null $featureMetadata Feature-specific metadata blob.
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly array $metadata,
        public readonly ?array $featureMetadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'metadata'         => $this->metadata,
            'feature_metadata' => $this->featureMetadata,
        ]);
        // `metadata` is required even when empty; filterNulls drops empty arrays,
        // so re-add it explicitly to preserve the required key.
        $typed['metadata'] = $this->metadata;
        return $this->mergeExtras($typed, $this->extras);
    }
}
