<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Increase the authorised amount on a payment without re-authenticating.
 *
 * @endpoint            POST /payments/{id}/incremental_authorization
 * @hyperswitch-source  2026-04-30
 */
final class IncrementalAuthorizationRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly int $amount,
        public readonly ?string $reason = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'amount' => $this->amount,
            'reason' => $this->reason,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
