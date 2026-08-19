<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Subscription\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\Currency;

/**
 * Catalog plan item returned by `/subscriptions/plans`. Plans come from the
 * upstream catalog provider (Chargebee, in-house, etc.) so the schema is
 * loose; we capture the most useful identifiers and let the rest pass
 * through `extras`.
 *
 * @endpoint            GET /subscriptions/plans
 * @hyperswitch-source  2026-04-30
 */
final class PlanResponse extends AbstractResponseDto
{
    /**
     * @param list<array<string, mixed>>|null $itemPrices
     * @param array<string, mixed>|null       $metadata
     * @param array<string, mixed>            $extras
     */
    public function __construct(
        public readonly string $planId,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $status = null,
        public readonly ?Currency $currency = null,
        public readonly ?int $price = null,
        public readonly ?string $period = null,
        public readonly ?int $periodUnit = null,
        public readonly ?array $itemPrices = null,
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
            'plan_id', 'id', 'name', 'description', 'status',
            'currency', 'price', 'period', 'period_unit',
            'item_prices', 'metadata',
        ];

        $itemPrices = $data['item_prices'] ?? null;
        $cleanedItemPrices = null;
        if (is_array($itemPrices)) {
            $cleanedItemPrices = [];
            foreach ($itemPrices as $row) {
                if (is_array($row)) {
                    /** @var array<string, mixed> $row */
                    $cleanedItemPrices[] = $row;
                }
            }
        }

        $metadata = $data['metadata'] ?? null;

        return new self(
            planId:      (string) ($data['plan_id'] ?? $data['id'] ?? ''),
            name:        isset($data['name'])        ? (string) $data['name']        : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            status:      isset($data['status'])      ? (string) $data['status']      : null,
            currency:    isset($data['currency'])
                ? Currency::from((string) $data['currency'])
                : null,
            price:       isset($data['price'])       ? (int)    $data['price']       : null,
            period:      isset($data['period'])      ? (string) $data['period']      : null,
            periodUnit:  isset($data['period_unit']) ? (int)    $data['period_unit'] : null,
            itemPrices:  $cleanedItemPrices,
            metadata:    is_array($metadata) ? $metadata : null,
            extras:      array_diff_key($data, array_flip($known)),
        );
    }
}
