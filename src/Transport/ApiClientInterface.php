<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Transport;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\AbstractResponseDto;

/**
 * Single entry point Resources use to dispatch a Hyperswitch call.
 *
 * Resources never call PSR-18 / curl / sockets directly. They build an
 * Endpoint + Request DTO + path/query params and hand them here.
 */
interface ApiClientInterface
{
    /**
     * Dispatch a request against the given Endpoint.
     *
     * @param array<string, scalar> $pathParams
     * @param array<string, scalar|array<string, scalar>> $queryParams
     * @param array<string, string> $extraHeaders
     *
     * @return AbstractResponseDto|array<int|string, mixed>|null
     *   Returns:
     *     - an instance of `Endpoint::$responseClass` when one is declared,
     *     - the raw decoded JSON array when no response DTO is declared,
     *     - null when the response body is empty.
     */
    public function execute(
        Endpoint $endpoint,
        ?AbstractRequestDto $request = null,
        array $pathParams = [],
        array $queryParams = [],
        array $extraHeaders = [],
    ): AbstractResponseDto|array|null;
}
