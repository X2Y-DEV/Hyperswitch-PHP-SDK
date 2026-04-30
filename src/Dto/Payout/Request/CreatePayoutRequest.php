<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payout\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\Common\Address;
use X2Y\HyperswitchClient\Dto\Common\BillingDetails;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\PayoutType;
use X2Y\HyperswitchClient\Enum\SetupFutureUsage;
use X2Y\HyperswitchClient\Enum\StorageType;

/**
 * Create a payout. Polymorphic: the same endpoint also creates payout-links
 * (set `payout_link: true`) and various payout-method variants (card / bank
 * / wallet) — the consumer populates the appropriate fields.
 *
 * `payoutMethodData` carries the connector-specific instrument payload (card
 * details, bank account info, wallet identifiers) and stays loose per the
 * connector-blob convention.
 *
 * @postman             Payouts / Payouts - Create
 * @endpoint            POST /payouts/create
 * @hyperswitch-source  2026-04-30
 */
final class CreatePayoutRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $payoutMethodData
     * @param array<string, mixed>|null $customer
     * @param array<string, mixed>|null $payoutLinkConfig
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly int $amount,
        public readonly Currency $currency,
        public readonly ?PayoutType $payoutType = null,
        public readonly ?string $customerId = null,
        public readonly ?array $customer = null,
        public readonly ?array $payoutMethodData = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?Address $shipping = null,
        public readonly ?string $description = null,
        public readonly ?string $entityType = null,
        public readonly ?bool $autoFulfill = null,
        public readonly ?bool $confirm = null,
        public readonly ?bool $payoutLink = null,
        public readonly ?array $payoutLinkConfig = null,
        public readonly ?string $returnUrl = null,
        public readonly ?StorageType $payoutTokenStorageType = null,
        public readonly ?SetupFutureUsage $setupFutureUsage = null,
        public readonly ?string $profileId = null,
        public readonly ?string $merchantOrderReferenceId = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'amount'                      => $this->amount,
            'currency'                    => $this->currency->value,
            'payout_type'                 => $this->payoutType?->value,
            'customer_id'                 => $this->customerId,
            'customer'                    => $this->customer,
            'payout_method_data'          => $this->payoutMethodData,
            'billing'                     => $this->billing?->toArray(),
            'shipping'                    => $this->shipping?->toArray(),
            'description'                 => $this->description,
            'entity_type'                 => $this->entityType,
            'auto_fulfill'                => $this->autoFulfill,
            'confirm'                     => $this->confirm,
            'payout_link'                 => $this->payoutLink,
            'payout_link_config'          => $this->payoutLinkConfig,
            'return_url'                  => $this->returnUrl,
            'payout_token_storage_type'   => $this->payoutTokenStorageType?->value,
            'setup_future_usage'          => $this->setupFutureUsage?->value,
            'profile_id'                  => $this->profileId,
            'merchant_order_reference_id' => $this->merchantOrderReferenceId,
            'metadata'                    => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
