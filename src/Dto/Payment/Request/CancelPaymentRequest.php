<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payment\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * @postman             Payments / Payments - Cancel
 * @endpoint            POST /payments/{id}/cancel
 * @hyperswitch-source  2026-04-30
 */
final class CancelPaymentRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $merchantConnectorDetails
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?string $cancellationReason = null,
        public readonly ?array $merchantConnectorDetails = null,
        public readonly ?bool $allKeysRequired = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'cancellation_reason'        => $this->cancellationReason,
            'merchant_connector_details' => $this->merchantConnectorDetails,
            'all_keys_required'          => $this->allKeysRequired,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
