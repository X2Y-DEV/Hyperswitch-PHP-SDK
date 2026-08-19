<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Available dispute filters. Values are raw strings so new connector / stage /
 * status values don't break the SDK.
 *
 * @openapi             "List all filters for disputes"
 * @endpoint            GET /disputes/filter
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class DisputeListFiltersResponse extends AbstractResponseDto
{
    /**
     * @param list<string>         $connector
     * @param list<string>         $currency
     * @param list<string>         $disputeStage
     * @param list<string>         $disputeStatus
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly array $connector = [],
        public readonly array $currency = [],
        public readonly array $disputeStage = [],
        public readonly array $disputeStatus = [],
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['connector', 'currency', 'dispute_stage', 'dispute_status'];

        return new self(
            connector:     self::stringListField($data, 'connector'),
            currency:      self::stringListField($data, 'currency'),
            disputeStage:  self::stringListField($data, 'dispute_stage'),
            disputeStatus: self::stringListField($data, 'dispute_status'),
            extras:        array_diff_key($data, array_flip($known)),
        );
    }
}
