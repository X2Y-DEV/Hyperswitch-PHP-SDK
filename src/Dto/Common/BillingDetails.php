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
        // v1 spec `Address` wraps contact number as a nested `PhoneDetails`
        // object { number, country_code } — there is no top-level
        // `phone_country_code` here (unlike the flat Customer schema).
        return $this->filterNulls([
            'address' => $this->address?->toArray(),
            'email'   => $this->email,
            'phone'   => [
                'number'       => $this->phone,
                'country_code' => $this->phoneCountryCode,
            ],
        ]);
    }
}
