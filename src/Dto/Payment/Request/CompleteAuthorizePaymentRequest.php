<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Enum\ThreeDsCompletionIndicator;

/**
 * @openapi             "Complete Authorize a Payment"
 * @endpoint            POST /payments/{id}/complete_authorize
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CompleteAuthorizePaymentRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?Address $shipping = null,
        public readonly ?string $clientSecret = null,
        public readonly ?ThreeDsCompletionIndicator $threedsMethodCompInd = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'shipping'                => $this->shipping?->toArray(),
            'client_secret'           => $this->clientSecret,
            'threeds_method_comp_ind' => $this->threedsMethodCompInd?->value,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
