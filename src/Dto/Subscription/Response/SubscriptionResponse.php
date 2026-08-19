<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\Currency;
use X2Y\HyperSwitchClient\Enum\SubscriptionStatus;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * Response from subscription create / confirm / update / retrieve / pause /
 * resume / cancel and list-item shape.
 *
 * HyperSwitch's subscription response embeds plan/billing-cycle metadata that
 * varies per upstream catalog provider (Chargebee, in-house). Top-level
 * scalars are typed; the deep catalog blobs follow the connector-blob
 * convention and stay in `extras`.
 *
 * @endpoint            GET /subscriptions/{id}
 * @hyperswitch-source  2026-04-30
 */
final class SubscriptionResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $subscriptionId,
        public readonly SubscriptionStatus $status,
        public readonly ?string $merchantId = null,
        public readonly ?string $customerId = null,
        public readonly ?string $merchantReferenceId = null,
        public readonly ?string $itemPriceId = null,
        public readonly ?string $planId = null,
        public readonly ?int $amount = null,
        public readonly ?Currency $currency = null,
        public readonly ?string $clientSecret = null,
        public readonly ?string $paymentId = null,
        public readonly ?\DateTimeImmutable $currentTermStart = null,
        public readonly ?\DateTimeImmutable $currentTermEnd = null,
        public readonly ?\DateTimeImmutable $nextBillingAt = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $modifiedAt = null,
        public readonly ?\DateTimeImmutable $cancelledAt = null,
        public readonly ?\DateTimeImmutable $pausedAt = null,
        public readonly ?array $metadata = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'subscription_id', 'status', 'merchant_id', 'customer_id',
            'merchant_reference_id', 'item_price_id', 'plan_id', 'amount',
            'currency', 'client_secret', 'payment_id',
            'current_term_start', 'current_term_end', 'next_billing_at',
            'created_at', 'modified_at', 'cancelled_at', 'paused_at',
            'metadata',
        ];

        $metadata = $data['metadata'] ?? null;

        return new self(
            subscriptionId:      (string) ($data['subscription_id'] ?? $data['id'] ?? ''),
            status:              SubscriptionStatus::from((string) $data['status']),
            merchantId:          isset($data['merchant_id'])           ? (string) $data['merchant_id']           : null,
            customerId:          isset($data['customer_id'])           ? (string) $data['customer_id']           : null,
            merchantReferenceId: isset($data['merchant_reference_id']) ? (string) $data['merchant_reference_id'] : null,
            itemPriceId:         isset($data['item_price_id'])         ? (string) $data['item_price_id']         : null,
            planId:              isset($data['plan_id'])               ? (string) $data['plan_id']               : null,
            amount:              isset($data['amount'])                ? (int)    $data['amount']                : null,
            currency:            isset($data['currency'])
                ? Currency::from((string) $data['currency'])
                : null,
            clientSecret:        isset($data['client_secret']) ? (string) $data['client_secret'] : null,
            paymentId:           isset($data['payment_id'])    ? (string) $data['payment_id']    : null,
            currentTermStart:    DateTimeParser::parse($data['current_term_start'] ?? null),
            currentTermEnd:      DateTimeParser::parse($data['current_term_end']   ?? null),
            nextBillingAt:       DateTimeParser::parse($data['next_billing_at']    ?? null),
            createdAt:           DateTimeParser::parse($data['created_at']         ?? null),
            modifiedAt:          DateTimeParser::parse($data['modified_at']        ?? null),
            cancelledAt:         DateTimeParser::parse($data['cancelled_at']       ?? null),
            pausedAt:            DateTimeParser::parse($data['paused_at']          ?? null),
            metadata:            is_array($metadata) ? $metadata : null,
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
