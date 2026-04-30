<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Subscription\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Enum\SubscriptionPauseOption;

/**
 * @postman             Subscriptions / Subscription - Pause
 * @endpoint            POST /subscriptions/{id}/pause
 * @hyperswitch-source  2026-04-30
 */
final class PauseSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly SubscriptionPauseOption $pauseOption = SubscriptionPauseOption::IMMEDIATELY,
        public readonly ?\DateTimeImmutable $pauseDate = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'pause_option' => $this->pauseOption->value,
            'pause_date'   => $this->pauseDate?->format(\DateTimeInterface::ATOM),
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
