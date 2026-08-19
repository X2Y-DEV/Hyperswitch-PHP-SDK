<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Submit Eligibility data for a Payment"
 * @endpoint            POST /payments/{id}/eligibility
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class EligibilityResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $sdkNextAction
     * @param array<string, mixed>|null $surchargeDetails
     * @param array<string, mixed>|null $amountDetails
     * @param array<string, mixed>|null $offerDetails
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly ?array $sdkNextAction = null,
        public readonly ?array $surchargeDetails = null,
        public readonly ?array $amountDetails = null,
        public readonly ?array $offerDetails = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['payment_id', 'sdk_next_action', 'surcharge_details', 'amount_details', 'offer_details'];

        return new self(
            paymentId:        (string) $data['payment_id'],
            sdkNextAction:    self::objectField($data, 'sdk_next_action'),
            surchargeDetails: self::objectField($data, 'surcharge_details'),
            amountDetails:    self::objectField($data, 'amount_details'),
            offerDetails:     self::objectField($data, 'offer_details'),
            extras:           array_diff_key($data, array_flip($known)),
        );
    }
}
