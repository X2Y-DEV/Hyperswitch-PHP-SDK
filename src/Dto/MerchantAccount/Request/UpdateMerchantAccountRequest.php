<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\MerchantAccount\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Update an existing merchant account. Same field set as create; every
 * field is optional and only the provided fields are updated.
 *
 * @endpoint            POST /accounts/{id}
 * @hyperswitch-source  2026-04-30
 */
final class UpdateMerchantAccountRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null    $merchantDetails
     * @param array<string, mixed>|null    $webhookDetails
     * @param list<array<string, mixed>>|null $primaryBusinessDetails
     * @param array<string, mixed>|null    $metadata
     */
    public function __construct(
        public readonly ?string $merchantName = null,
        public readonly ?string $lockerId = null,
        public readonly ?string $returnUrl = null,
        public readonly ?array $merchantDetails = null,
        public readonly ?array $webhookDetails = null,
        public readonly ?array $primaryBusinessDetails = null,
        public readonly ?array $metadata = null,
        public readonly ?bool $subMerchantsEnabled = null,
        public readonly ?string $parentMerchantId = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'merchant_name'            => $this->merchantName,
            'locker_id'                => $this->lockerId,
            'return_url'               => $this->returnUrl,
            'merchant_details'         => $this->merchantDetails,
            'webhook_details'          => $this->webhookDetails,
            'primary_business_details' => $this->primaryBusinessDetails,
            'metadata'                 => $this->metadata,
            'sub_merchants_enabled'    => $this->subMerchantsEnabled,
            'parent_merchant_id'       => $this->parentMerchantId,
        ]);
    }
}
