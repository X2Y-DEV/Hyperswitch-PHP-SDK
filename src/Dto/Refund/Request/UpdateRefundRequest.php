<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Refund\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * Update reason / metadata on an existing refund.
 *
 * @postman             Refunds / Refunds - Update
 * @endpoint            POST /refunds/{id}
 * @hyperswitch-source  2026-04-30
 */
final class UpdateRefundRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public readonly ?string $reason = null,
        public readonly ?array $metadata = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'reason'   => $this->reason,
            'metadata' => $this->metadata,
        ]);
    }
}
