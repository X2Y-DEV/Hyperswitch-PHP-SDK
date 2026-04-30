<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Subscription\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Enum\SubscriptionChargesHandling;
use X2Y\HyperswitchClient\Enum\SubscriptionResumeOption;
use X2Y\HyperswitchClient\Enum\SubscriptionUnpaidInvoicesHandling;

/**
 * @postman             Subscriptions / Subscription - Resume
 * @endpoint            POST /subscriptions/{id}/resume
 * @hyperswitch-source  2026-04-30
 */
final class ResumeSubscriptionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly SubscriptionResumeOption $resumeOption = SubscriptionResumeOption::IMMEDIATELY,
        public readonly ?\DateTimeImmutable $resumeDate = null,
        public readonly ?SubscriptionUnpaidInvoicesHandling $unpaidInvoicesHandling = null,
        public readonly ?SubscriptionChargesHandling $chargesHandling = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'resume_option'            => $this->resumeOption->value,
            'resume_date'              => $this->resumeDate?->format(\DateTimeInterface::ATOM),
            'unpaid_invoices_handling' => $this->unpaidInvoicesHandling?->value,
            'charges_handling'         => $this->chargesHandling?->value,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
