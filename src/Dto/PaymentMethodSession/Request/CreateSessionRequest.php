<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\BillingDetails;
use X2Y\HyperSwitchClient\Enum\StorageType;

/**
 * Create a payment-method session — a short-lived envelope a client-side
 * tokenisation flow attaches to.
 *
 * @endpoint            POST /v1/payment-method-sessions
 * @hyperswitch-source  2026-04-30
 */
final class CreateSessionRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $pspTokenization
     * @param array<string, mixed>|null $networkTokenization
     * @param array<string, mixed>|null $tokenizationData
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $customerId,
        public readonly ?string $returnUrl = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?StorageType $storageType = null,
        public readonly ?array $pspTokenization = null,
        public readonly ?array $networkTokenization = null,
        public readonly ?array $tokenizationData = null,
        public readonly ?bool $keepAlive = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'customer_id'         => $this->customerId,
            'return_url'          => $this->returnUrl,
            'billing'             => $this->billing?->toArray(),
            'storage_type'        => $this->storageType?->value,
            'psp_tokenization'    => $this->pspTokenization,
            'network_tokenization'=> $this->networkTokenization,
            'tokenization_data'   => $this->tokenizationData,
            'keep_alive'          => $this->keepAlive,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
