<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payout\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\PayoutStatus;
use X2Y\HyperswitchClient\Enum\PayoutType;
use X2Y\HyperswitchClient\Internal\DateTimeParser;

/**
 * Response from /payouts create / update / confirm / fulfill / cancel /
 * retrieve.
 *
 * @postman             Payouts / Payouts - Retrieve
 * @endpoint            GET /payouts/{id}
 * @hyperswitch-source  2026-04-30
 */
final class PayoutResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $payoutId,
        public readonly PayoutStatus $status,
        public readonly int $amount,
        public readonly Currency $currency,
        public readonly ?PayoutType $payoutType = null,
        public readonly ?string $customerId = null,
        public readonly ?string $description = null,
        public readonly ?string $clientSecret = null,
        public readonly ?string $payoutLink = null,
        public readonly ?string $entityType = null,
        public readonly ?string $errorMessage = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $connector = null,
        public readonly ?string $merchantConnectorId = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
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
            'payout_id', 'status', 'amount', 'currency', 'payout_type',
            'customer_id', 'description', 'client_secret', 'payout_link',
            'entity_type', 'error_message', 'error_code', 'connector',
            'merchant_connector_id', 'created_at',
        ];

        return new self(
            payoutId:    (string) $data['payout_id'],
            status:      PayoutStatus::from((string) $data['status']),
            amount:      (int) $data['amount'],
            currency:    Currency::from((string) $data['currency']),
            payoutType:  isset($data['payout_type'])
                ? PayoutType::from((string) $data['payout_type'])
                : null,
            customerId:          isset($data['customer_id'])           ? (string) $data['customer_id']           : null,
            description:         isset($data['description'])           ? (string) $data['description']           : null,
            clientSecret:        isset($data['client_secret'])         ? (string) $data['client_secret']         : null,
            payoutLink:          isset($data['payout_link'])           ? (string) $data['payout_link']           : null,
            entityType:          isset($data['entity_type'])           ? (string) $data['entity_type']           : null,
            errorMessage:        isset($data['error_message'])         ? (string) $data['error_message']         : null,
            errorCode:           isset($data['error_code'])            ? (string) $data['error_code']            : null,
            connector:           isset($data['connector'])             ? (string) $data['connector']             : null,
            merchantConnectorId: isset($data['merchant_connector_id']) ? (string) $data['merchant_connector_id'] : null,
            createdAt:           DateTimeParser::parse($data['created_at'] ?? null),
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
