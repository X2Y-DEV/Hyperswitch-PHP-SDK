<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Customer\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Response from DELETE /customers/{id}.
 *
 * HyperSwitch acknowledges the delete with `customer_id` and a flag indicating
 * the soft-delete state.
 *
 * @endpoint            DELETE /customers/{id}
 * @hyperswitch-source  2026-04-30
 */
final class CustomerDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly bool $customerDeleted,
        public readonly ?bool $addressDeleted = null,
        public readonly ?bool $paymentMethodsDeleted = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['customer_id', 'customer_deleted', 'address_deleted', 'payment_methods_deleted'];

        return new self(
            customerId:            (string) $data['customer_id'],
            customerDeleted:       (bool)   ($data['customer_deleted'] ?? false),
            addressDeleted:        isset($data['address_deleted'])
                ? (bool) $data['address_deleted']
                : null,
            paymentMethodsDeleted: isset($data['payment_methods_deleted'])
                ? (bool) $data['payment_methods_deleted']
                : null,
            extras:                array_diff_key($data, array_flip($known)),
        );
    }
}
