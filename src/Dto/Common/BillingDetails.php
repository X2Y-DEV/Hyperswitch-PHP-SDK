<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Common;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Billing details on a payment — Address plus contact fields.
 *
 * @postman             Common / BillingDetails
 * @hyperswitch-source  2026-04-30
 */
final class BillingDetails extends AbstractRequestDto
{
    public function __construct(
        public readonly ?Address $address = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $phoneCountryCode = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'address'            => $this->address?->toArray(),
            'email'              => $this->email,
            'phone'              => $this->phone,
            'phone_country_code' => $this->phoneCountryCode,
        ]);
    }
}
