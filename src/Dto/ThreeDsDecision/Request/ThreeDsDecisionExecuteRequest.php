<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Execute 3DS Decision Rule"
 * @endpoint            POST /three_ds_decision/execute
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ThreeDsDecisionExecuteRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>      $payment        Payment context for the rule.
     * @param array<string, mixed>|null $acquirer
     * @param array<string, mixed>|null $issuer
     * @param array<string, mixed>|null $customerDevice
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $routingId,
        public readonly array $payment,
        public readonly ?array $acquirer = null,
        public readonly ?array $issuer = null,
        public readonly ?array $customerDevice = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'routing_id'      => $this->routingId,
            'acquirer'        => $this->acquirer,
            'issuer'          => $this->issuer,
            'customer_device' => $this->customerDevice,
            'payment_method'  => $this->paymentMethod,
            'metadata'        => $this->metadata,
        ]);
        // `payment` is required even when empty; re-add after filterNulls.
        $typed['payment'] = $this->payment;
        return $this->mergeExtras($typed, $this->extras);
    }
}
