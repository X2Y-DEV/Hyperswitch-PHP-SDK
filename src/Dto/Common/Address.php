<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Common;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Postal address used in billing, shipping, and merchant_details.
 *
 * @hyperswitch-source  2026-04-30
 */
final class Address extends AbstractRequestDto
{
    public function __construct(
        public readonly ?string $line1 = null,
        public readonly ?string $line2 = null,
        public readonly ?string $line3 = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zip = null,
        public readonly ?string $country = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'line1'      => $this->line1,
            'line2'      => $this->line2,
            'line3'      => $this->line3,
            'city'       => $this->city,
            'state'      => $this->state,
            'zip'        => $this->zip,
            'country'    => $this->country,
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            line1:     isset($data['line1'])      ? (string) $data['line1']      : null,
            line2:     isset($data['line2'])      ? (string) $data['line2']      : null,
            line3:     isset($data['line3'])      ? (string) $data['line3']      : null,
            city:      isset($data['city'])       ? (string) $data['city']       : null,
            state:     isset($data['state'])      ? (string) $data['state']      : null,
            zip:       isset($data['zip'])        ? (string) $data['zip']        : null,
            country:   isset($data['country'])    ? (string) $data['country']    : null,
            firstName: isset($data['first_name']) ? (string) $data['first_name'] : null,
            lastName:  isset($data['last_name'])  ? (string) $data['last_name']  : null,
        );
    }
}
