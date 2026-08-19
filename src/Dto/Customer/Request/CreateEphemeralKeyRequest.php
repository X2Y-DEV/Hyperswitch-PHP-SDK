<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Customer\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Mint a short-lived ephemeral key bound to a customer. The merchant server
 * calls this with its merchant key, then hands the resulting `secret` to the
 * client (browser / mobile app) for use with publishable-key-tier endpoints.
 *
 * @endpoint            POST /ephemeral_keys
 * @hyperswitch-source  2026-04-30
 */
final class CreateEphemeralKeyRequest extends AbstractRequestDto
{
    public function __construct(
        public readonly string $customerId,
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
        ];
    }
}
