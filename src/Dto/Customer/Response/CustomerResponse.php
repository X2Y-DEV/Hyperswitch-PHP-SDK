<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Customer\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * Response from /customers create / retrieve / update and /customers/list items.
 *
 * @endpoint            GET /customers/{id}
 * @hyperswitch-source  2026-04-30
 */
final class CustomerResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly ?string $email = null,
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $phoneCountryCode = null,
        public readonly ?string $description = null,
        public readonly ?Address $address = null,
        public readonly ?array $metadata = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?string $defaultPaymentMethodId = null,
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
            'customer_id', 'email', 'name', 'phone', 'phone_country_code',
            'description', 'address', 'metadata', 'created_at',
            'default_payment_method_id',
        ];

        $address  = $data['address']  ?? null;
        $metadata = $data['metadata'] ?? null;

        return new self(
            customerId:             (string) $data['customer_id'],
            email:                  isset($data['email'])              ? (string) $data['email']              : null,
            name:                   isset($data['name'])               ? (string) $data['name']               : null,
            phone:                  isset($data['phone'])              ? (string) $data['phone']              : null,
            phoneCountryCode:       isset($data['phone_country_code']) ? (string) $data['phone_country_code'] : null,
            description:            isset($data['description'])        ? (string) $data['description']        : null,
            address:                is_array($address)                 ? Address::fromArray($address)         : null,
            metadata:               is_array($metadata)                ? $metadata                            : null,
            createdAt:              DateTimeParser::parse($data['created_at'] ?? null),
            defaultPaymentMethodId: isset($data['default_payment_method_id'])
                ? (string) $data['default_payment_method_id']
                : null,
            extras:                 array_diff_key($data, array_flip($known)),
        );
    }
}
