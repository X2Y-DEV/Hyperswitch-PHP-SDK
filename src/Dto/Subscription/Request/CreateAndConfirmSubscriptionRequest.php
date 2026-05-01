<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Common\BillingDetails;

/**
 * Create and confirm a subscription in a single call. Same general shape as
 * create, but `paymentDetails` carries a complete payment_method_data block
 * with customer_acceptance — the call returns an active subscription.
 *
 * @postman             Subscriptions / Subscription - Create + Confirm
 * @endpoint            POST /subscriptions
 * @hyperswitch-source  2026-04-30
 */
final class CreateAndConfirmSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>      $paymentDetails  required: payment_method_data + customer_acceptance
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly string $itemPriceId,
        public readonly array $paymentDetails,
        public readonly ?string $merchantReferenceId = null,
        public readonly ?string $description = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?Address $shipping = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'customer_id'           => $this->customerId,
            'item_price_id'         => $this->itemPriceId,
            'payment_details'       => $this->paymentDetails,
            'merchant_reference_id' => $this->merchantReferenceId,
            'description'           => $this->description,
            'billing'               => $this->billing?->toArray(),
            'shipping'              => $this->shipping?->toArray(),
            'metadata'              => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
