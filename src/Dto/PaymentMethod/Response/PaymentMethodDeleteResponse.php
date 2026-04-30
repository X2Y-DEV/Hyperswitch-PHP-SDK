<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\PaymentMethod\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;

/**
 * @postman             Payment Methods - Modular Payment / Payment Methods - Delete
 * @endpoint            DELETE /v1/payment-methods/{id}
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $paymentMethodId,
        public readonly bool $deleted,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['payment_method_id', 'deleted'];

        return new self(
            paymentMethodId: (string) ($data['payment_method_id'] ?? ''),
            deleted:         (bool)   ($data['deleted'] ?? false),
            extras:          array_diff_key($data, array_flip($known)),
        );
    }
}
