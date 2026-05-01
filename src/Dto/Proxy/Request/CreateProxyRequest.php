<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Proxy\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Forward a vault-tokenised request to a destination URL with the
 * sensitive instrument data hydrated from HyperSwitch's vault.
 *
 * @postman             Proxy / Proxy - Create
 * @endpoint            POST /v1/proxy
 * @hyperswitch-source  2026-04-30
 */
final class CreateProxyRequest extends AbstractRequestDto
{
    /**
     * @param array<string, string>     $headers
     * @param array<string, mixed>      $requestBody
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $destinationUrl,
        public readonly string $method,
        public readonly array $requestBody,
        public readonly array $headers = [],
        public readonly ?string $token = null,
        public readonly ?string $tokenType = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'destination_url' => $this->destinationUrl,
            'method'          => $this->method,
            'headers'         => $this->headers,
            'request_body'    => $this->requestBody,
            'token'           => $this->token,
            'token_type'      => $this->tokenType,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
