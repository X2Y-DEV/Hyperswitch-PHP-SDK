<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Customer\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\Common\Address;

/**
 * Update an existing Hyperswitch customer. Same field set as create; every
 * field is optional and only the provided fields are updated.
 *
 * @postman             Customers / Update Customer
 * @endpoint            POST /customers/{id}
 * @hyperswitch-source  2026-04-30
 */
final class UpdateCustomerRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
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
