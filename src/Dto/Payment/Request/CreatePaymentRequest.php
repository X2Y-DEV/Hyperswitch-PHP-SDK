<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payment\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\Common\Address;
use X2Y\HyperswitchClient\Dto\Common\BillingDetails;
use X2Y\HyperswitchClient\Enum\AuthenticationType;
use X2Y\HyperswitchClient\Enum\CaptureMethod;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\SetupFutureUsage;

/**
 * Create a new payment intent.
 *
 * Stub set of fields — the full Postman body has 60+ optional fields. AI maintenance
 * extends this DTO to cover them all; the structure here is the template.
 *
 * @postman             Payments / Payments - Create
 * @endpoint            POST /payments
 * @hyperswitch-source  2026-04-30
 */
final class CreatePaymentRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public readonly int $amount,
        public readonly Currency $currency,
        public readonly ?CaptureMethod $captureMethod = null,
        public readonly ?AuthenticationType $authenticationType = null,
        public readonly ?SetupFutureUsage $setupFutureUsage = null,
        public readonly ?string $customerId = null,
        public readonly ?string $description = null,
        public readonly ?string $statementDescriptorName = null,
        public readonly ?string $returnUrl = null,
        public readonly ?bool $confirm = null,
        public readonly ?bool $offSession = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?Address $shipping = null,
        public readonly ?array $metadata = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'amount'                    => $this->amount,
            'currency'                  => $this->currency->value,
            'capture_method'            => $this->captureMethod?->value,
            'authentication_type'       => $this->authenticationType?->value,
            'setup_future_usage'        => $this->setupFutureUsage?->value,
            'customer_id'               => $this->customerId,
            'description'               => $this->description,
            'statement_descriptor_name' => $this->statementDescriptorName,
            'return_url'                => $this->returnUrl,
            'confirm'                   => $this->confirm,
            'off_session'               => $this->offSession,
            'billing'                   => $this->billing?->toArray(),
            'shipping'                  => $this->shipping?->toArray(),
            'metadata'                  => $this->metadata,
        ]);
    }
}
