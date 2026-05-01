<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Common\BillingDetails;
use X2Y\HyperSwitchClient\Enum\Currency;
use X2Y\HyperSwitchClient\Enum\PayoutType;

/**
 * Update an existing payout before fulfillment. Same shape as create with
 * every field optional.
 *
 * @postman             Payouts / Payouts - Update
 * @endpoint            PUT /payouts/{id}
 * @hyperswitch-source  2026-04-30
 */
final class UpdatePayoutRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $payoutMethodData
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?int $amount = null,
        public readonly ?Currency $currency = null,
        public readonly ?PayoutType $payoutType = null,
        public readonly ?string $customerId = null,
        public readonly ?array $payoutMethodData = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?Address $shipping = null,
        public readonly ?string $description = null,
        public readonly ?bool $autoFulfill = null,
        public readonly ?bool $confirm = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'amount'             => $this->amount,
            'currency'           => $this->currency?->value,
            'payout_type'        => $this->payoutType?->value,
            'customer_id'        => $this->customerId,
            'payout_method_data' => $this->payoutMethodData,
            'billing'            => $this->billing?->toArray(),
            'shipping'           => $this->shipping?->toArray(),
            'description'        => $this->description,
            'auto_fulfill'       => $this->autoFulfill,
            'confirm'            => $this->confirm,
            'metadata'           => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
