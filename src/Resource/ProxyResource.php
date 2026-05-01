<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Proxy\Request\CreateProxyRequest;
use X2Y\HyperswitchClient\Dto\Proxy\Response\ProxyResponse;
use X2Y\HyperswitchClient\Endpoint\ProxyEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * @postman             Proxy
 * @hyperswitch-source  2026-04-30
 */
final class ProxyResource extends AbstractResource
{
    public function create(CreateProxyRequest $request): ProxyResponse
    {
        $response = $this->execute(ProxyEndpoints::create(), request: $request);
        if (!$response instanceof ProxyResponse) {
            throw new HyperswitchApiException('Unexpected response type from Proxy::create.');
        }
        return $response;
    }
}
