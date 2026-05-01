<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Proxy\Request\CreateProxyRequest;
use X2Y\HyperSwitchClient\Dto\Proxy\Response\ProxyResponse;
use X2Y\HyperSwitchClient\Endpoint\ProxyEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

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
            throw new HyperSwitchApiException('Unexpected response type from Proxy::create.');
        }
        return $response;
    }
}
