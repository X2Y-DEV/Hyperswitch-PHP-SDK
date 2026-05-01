<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @postman             Payments / Payments - Capture
 * @endpoint            POST /payments/{id}/capture
 * @hyperswitch-source  2026-04-30
 */
final class CapturePaymentRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $merchantConnectorDetails
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?int $amountToCapture = null,
        public readonly ?string $statementDescriptorName = null,
        public readonly ?string $statementDescriptorPrefix = null,
        public readonly ?string $statementDescriptorSuffix = null,
        public readonly ?array $merchantConnectorDetails = null,
        public readonly ?bool $allKeysRequired = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'amount_to_capture'            => $this->amountToCapture,
            'statement_descriptor_name'    => $this->statementDescriptorName,
            'statement_descriptor_prefix'  => $this->statementDescriptorPrefix,
            'statement_descriptor_suffix'  => $this->statementDescriptorSuffix,
            'merchant_connector_details'   => $this->merchantConnectorDetails,
            'all_keys_required'            => $this->allKeysRequired,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
