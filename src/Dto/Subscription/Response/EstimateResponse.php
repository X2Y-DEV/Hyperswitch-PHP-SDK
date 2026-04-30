<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Subscription\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\Currency;

/**
 * Cost estimate for a hypothetical subscription. The deeper line-item /
 * proration / tax breakdowns vary by upstream catalog; preserved in extras.
 *
 * @postman             Subscriptions / Subscription - Estimate
 * @endpoint            GET /subscriptions/estimate
 * @hyperswitch-source  2026-04-30
 */
final class EstimateResponse extends AbstractResponseDto
{
    /**
     * @param list<array<string, mixed>>|null $lineItems
     * @param array<string, mixed>|null       $taxes
     * @param array<string, mixed>            $extras
     */
    public function __construct(
        public readonly ?int $subTotal = null,
        public readonly ?int $total = null,
        public readonly ?int $amountDue = null,
        public readonly ?Currency $currency = null,
        public readonly ?array $lineItems = null,
        public readonly ?array $taxes = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['sub_total', 'total', 'amount_due', 'currency', 'line_items', 'taxes'];

        $lineItems = $data['line_items'] ?? null;
        $cleanedLineItems = null;
        if (is_array($lineItems)) {
            $cleanedLineItems = [];
            foreach ($lineItems as $row) {
                if (is_array($row)) {
                    /** @var array<string, mixed> $row */
                    $cleanedLineItems[] = $row;
                }
            }
        }

        $taxes = $data['taxes'] ?? null;

        return new self(
            subTotal:  isset($data['sub_total'])  ? (int) $data['sub_total']  : null,
            total:     isset($data['total'])      ? (int) $data['total']      : null,
            amountDue: isset($data['amount_due']) ? (int) $data['amount_due'] : null,
            currency:  isset($data['currency'])
                ? Currency::from((string) $data['currency'])
                : null,
            lineItems: $cleanedLineItems,
            taxes:     is_array($taxes) ? $taxes : null,
            extras:    array_diff_key($data, array_flip($known)),
        );
    }
}
