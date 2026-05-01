<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Create a subscription without confirming it. Returns a subscription in
 * pending state; callers must follow up with `confirm` once payment details
 * are collected.
 *
 * `paymentDetails` mirrors a Payment-create body (capture method, auth type,
 * setup future usage, return URL, payment_method_data, etc.) — connector-blob
 * convention applies.
 *
 * @postman             Subscriptions / Subscription - Create
 * @endpoint            POST /subscriptions/create
 * @hyperswitch-source  2026-04-30
 */
final class CreateSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $paymentDetails
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly string $itemPriceId,
        public readonly ?string $merchantReferenceId = null,
        public readonly ?string $description = null,
        public readonly ?array $paymentDetails = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'customer_id'           => $this->customerId,
            'item_price_id'         => $this->itemPriceId,
            'merchant_reference_id' => $this->merchantReferenceId,
            'description'           => $this->description,
            'payment_details'       => $this->paymentDetails,
            'metadata'              => $this->metadata,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
