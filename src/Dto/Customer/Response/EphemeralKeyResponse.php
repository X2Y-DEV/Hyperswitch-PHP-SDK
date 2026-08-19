<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Customer\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

/**
 * Response from POST /ephemeral_keys.
 *
 * `secret` is the value handed to client-side code (browser/mobile) to
 * authorise publishable-key-tier calls scoped to this customer. Treat as
 * sensitive — log carefully.
 *
 * @endpoint            POST /ephemeral_keys
 * @hyperswitch-source  2026-04-30
 */
final class EphemeralKeyResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly string $id,
        public readonly string $secret,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $expires = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['customer_id', 'id', 'secret', 'created_at', 'expires'];

        return new self(
            customerId: (string) $data['customer_id'],
            id:         (string) $data['id'],
            secret:     (string) $data['secret'],
            createdAt:  DateTimeParser::parse($data['created_at'] ?? null),
            expires:    DateTimeParser::parse($data['expires']    ?? null),
            extras:     array_diff_key($data, array_flip($known)),
        );
    }
}
