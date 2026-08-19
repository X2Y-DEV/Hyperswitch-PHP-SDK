<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Customer\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\Address;

/**
 * Create a HyperSwitch customer record.
 *
 * @endpoint            POST /customers
 * @hyperswitch-source  2026-04-30
 */
final class CreateCustomerRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public readonly ?string $customerId = null,
        public readonly ?string $email = null,
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $phoneCountryCode = null,
        public readonly ?string $description = null,
        public readonly ?Address $address = null,
        public readonly ?array $metadata = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'customer_id'        => $this->customerId,
            'email'              => $this->email,
            'name'               => $this->name,
            'phone'              => $this->phone,
            'phone_country_code' => $this->phoneCountryCode,
            'description'        => $this->description,
            'address'            => $this->address?->toArray(),
            'metadata'           => $this->metadata,
        ]);
    }
}
