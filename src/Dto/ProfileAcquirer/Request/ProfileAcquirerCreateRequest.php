<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Create a Profile Acquirer"
 * @endpoint            POST /profile_acquirers
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ProfileAcquirerCreateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras Optional fields (acquirer_country_code, acquirer_ica, is_default, …).
     */
    public function __construct(
        public readonly string $profileId,
        public readonly string $merchantName,
        public readonly string $network,
        public readonly string $acquirerBin,
        public readonly string $acquirerAssignedMerchantId,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = [
            'profile_id'                    => $this->profileId,
            'merchant_name'                 => $this->merchantName,
            'network'                       => $this->network,
            'acquirer_bin'                  => $this->acquirerBin,
            'acquirer_assigned_merchant_id' => $this->acquirerAssignedMerchantId,
        ];
        return $this->mergeExtras($typed, $this->extras);
    }
}
