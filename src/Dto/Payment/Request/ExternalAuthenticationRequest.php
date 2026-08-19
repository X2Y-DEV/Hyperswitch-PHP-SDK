<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\DeviceChannel;
use X2Y\HyperSwitchClient\Enum\ThreeDsCompletionIndicator;

/**
 * @openapi             "Initiate external authentication for a Payment"
 * @endpoint            POST /payments/{id}/3ds/authentication
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ExternalAuthenticationRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $sdkInformation Browser/app SDK metadata; shape varies by device channel.
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly DeviceChannel $deviceChannel,
        public readonly ThreeDsCompletionIndicator $threedsMethodCompInd,
        public readonly ?string $clientSecret = null,
        public readonly ?array $sdkInformation = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'device_channel'          => $this->deviceChannel->value,
            'threeds_method_comp_ind' => $this->threedsMethodCompInd->value,
            'client_secret'           => $this->clientSecret,
            'sdk_information'         => $this->sdkInformation,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
