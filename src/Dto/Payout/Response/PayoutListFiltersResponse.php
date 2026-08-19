<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Available payout filters (connectors, currencies, statuses, methods) for a
 * merchant. Values are returned as raw strings so new connector/method values
 * don't break the SDK.
 *
 * @openapi             "List available payout filters"
 * @endpoint            POST /payouts/filter
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PayoutListFiltersResponse extends AbstractResponseDto
{
    /**
     * @param list<string>         $connector
     * @param list<string>         $currency
     * @param list<string>         $status
     * @param list<string>         $payoutMethod
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly array $connector = [],
        public readonly array $currency = [],
        public readonly array $status = [],
        public readonly array $payoutMethod = [],
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['connector', 'currency', 'status', 'payout_method'];

        return new self(
            connector:    self::stringListField($data, 'connector'),
            currency:     self::stringListField($data, 'currency'),
            status:       self::stringListField($data, 'status'),
            payoutMethod: self::stringListField($data, 'payout_method'),
            extras:       array_diff_key($data, array_flip($known)),
        );
    }
}
