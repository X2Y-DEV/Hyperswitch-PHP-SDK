<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\IntentStatus;

/**
 * @openapi             "Update Metadata for a Payment"
 * @endpoint            POST /payments/{id}/update_metadata
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class UpdateMetadataPaymentResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>|null $featureMetadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly ?array $metadata = null,
        public readonly ?IntentStatus $status = null,
        public readonly ?array $featureMetadata = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['payment_id', 'metadata', 'status', 'feature_metadata'];

        return new self(
            paymentId:       (string) $data['payment_id'],
            metadata:        self::objectField($data, 'metadata'),
            status:          isset($data['status']) ? IntentStatus::from((string) $data['status']) : null,
            featureMetadata: self::objectField($data, 'feature_metadata'),
            extras:          array_diff_key($data, array_flip($known)),
        );
    }
}
