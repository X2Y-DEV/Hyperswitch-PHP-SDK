<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Relay\Request\CreateRelayRequest;
use X2Y\HyperswitchClient\Dto\Relay\Response\RelayResponse;
use X2Y\HyperswitchClient\Endpoint\RelayEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * Typed facade over the Hyperswitch Relay group.
 *
 * @postman             Relay
 * @hyperswitch-source  2026-04-30
 */
final class RelayResource extends AbstractResource
{
    public function create(CreateRelayRequest $request): RelayResponse
    {
        $response = $this->execute(RelayEndpoints::create(), request: $request);
        if (!$response instanceof RelayResponse) {
            throw new HyperswitchApiException('Unexpected response type from Relay::create.');
        }
        return $response;
    }

    public function retrieve(string $relayId): RelayResponse
    {
        $response = $this->execute(
            RelayEndpoints::retrieve(),
            pathParams: ['id' => $relayId],
        );
        if (!$response instanceof RelayResponse) {
            throw new HyperswitchApiException('Unexpected response type from Relay::retrieve.');
        }
        return $response;
    }
}
