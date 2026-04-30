<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payment\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * @postman             Payments / Payments - Cancel Post Capture
 * @endpoint            POST /payments/{id}/cancel_post_capture
 * @hyperswitch-source  2026-04-30
 */
final class CancelPostCapturePaymentRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $cancellationReason = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'cancellation_reason' => $this->cancellationReason,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
