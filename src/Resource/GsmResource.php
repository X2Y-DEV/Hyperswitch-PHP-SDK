<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmCreateRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmKeyRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmUpdateRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Response\GsmDeleteResponse;
use X2Y\HyperSwitchClient\Dto\Gsm\Response\GsmResponse;
use X2Y\HyperSwitchClient\Endpoint\GsmEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * @openapi             tag: Gsm
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmResource extends AbstractResource
{
    /** @openapi "Create Gsm Rule" */
    public function create(GsmCreateRequest $request): GsmResponse
    {
        return $this->asGsm($this->execute(GsmEndpoints::create(), request: $request), 'create');
    }

    /** @openapi "Retrieve Gsm Rule" */
    public function retrieve(GsmKeyRequest $request): GsmResponse
    {
        return $this->asGsm($this->execute(GsmEndpoints::retrieve(), request: $request), 'retrieve');
    }

    /** @openapi "Update Gsm Rule" */
    public function update(GsmUpdateRequest $request): GsmResponse
    {
        return $this->asGsm($this->execute(GsmEndpoints::update(), request: $request), 'update');
    }

    /** @openapi "Delete Gsm Rule" */
    public function delete(GsmKeyRequest $request): GsmDeleteResponse
    {
        $response = $this->execute(GsmEndpoints::delete(), request: $request);
        if (!$response instanceof GsmDeleteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Gsm::delete.');
        }
        return $response;
    }

    private function asGsm(mixed $response, string $context): GsmResponse
    {
        if (!$response instanceof GsmResponse) {
            throw new HyperSwitchApiException("Unexpected response type from Gsm::{$context}.");
        }
        return $response;
    }
}
