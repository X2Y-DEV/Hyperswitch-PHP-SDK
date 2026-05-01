<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Mandate\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\Currency;
use X2Y\HyperSwitchClient\Enum\MandateStatus;
use X2Y\HyperSwitchClient\Enum\MandateType;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * Response from GET /mandates/{id}, GET /customers/{id}/mandates (item),
 * and POST /mandates/revoke/{id}.
 *
 * @postman             Mandates / Mandate - List details of a mandate
 * @endpoint            GET /mandates/{id}
 * @hyperswitch-source  2026-04-30
 */
final class MandateResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $paymentMethodData
     * @param array<string, mixed>|null $customerAcceptance
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $mandateId,
        public readonly MandateStatus $status,
        public readonly ?string $customerId = null,
        public readonly ?MandateType $mandateType = null,
        public readonly ?int $amount = null,
        public readonly ?Currency $currency = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $paymentMethodId = null,
        public readonly ?array $paymentMethodData = null,
        public readonly ?array $customerAcceptance = null,
        public readonly ?\DateTimeImmutable $startDate = null,
        public readonly ?\DateTimeImmutable $endDate = null,
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
            'mandate_id', 'status', 'customer_id', 'mandate_type', 'amount',
            'currency', 'payment_method', 'payment_method_id',
            'payment_method_data', 'customer_acceptance',
            'start_date', 'end_date', 'created_at',
        ];

        $pmd = $data['payment_method_data'] ?? null;
        $ca  = $data['customer_acceptance'] ?? null;

        return new self(
            mandateId:          (string) $data['mandate_id'],
            status:             MandateStatus::from((string) $data['status']),
            customerId:         isset($data['customer_id'])     ? (string) $data['customer_id']     : null,
            mandateType:        isset($data['mandate_type'])
                ? MandateType::from((string) $data['mandate_type'])
                : null,
            amount:             isset($data['amount'])           ? (int) $data['amount']           : null,
            currency:           isset($data['currency'])
                ? Currency::from((string) $data['currency'])
                : null,
            paymentMethod:      isset($data['payment_method'])    ? (string) $data['payment_method']    : null,
            paymentMethodId:    isset($data['payment_method_id']) ? (string) $data['payment_method_id'] : null,
            paymentMethodData:  is_array($pmd) ? $pmd : null,
            customerAcceptance: is_array($ca)  ? $ca  : null,
            startDate:          DateTimeParser::parse($data['start_date'] ?? null),
            endDate:            DateTimeParser::parse($data['end_date']   ?? null),
            createdAt:          DateTimeParser::parse($data['created_at'] ?? null),
            extras:             array_diff_key($data, array_flip($known)),
        );
    }
}
