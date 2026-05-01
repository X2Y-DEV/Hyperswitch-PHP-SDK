<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Platform\Request\ConvertToPlatformRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Request\CreateOrganizationRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Request\CreatePlatformUserRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Response\PlatformResponse;
use X2Y\HyperSwitchClient\Endpoint\PlatformEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Platform group. Admin-tier.
 *
 * @postman             Platform
 * @hyperswitch-source  2026-04-30
 */
final class PlatformResource extends AbstractResource
{
    public function createOrganization(CreateOrganizationRequest $request): PlatformResponse
    {
        $response = $this->execute(PlatformEndpoints::createOrganization(), request: $request);
        if (!$response instanceof PlatformResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Platform::createOrganization.');
        }
        return $response;
    }

    public function convertToPlatform(string $organizationId, ConvertToPlatformRequest $request): PlatformResponse
    {
        $response = $this->execute(
            PlatformEndpoints::convertToPlatform(),
            request:    $request,
            pathParams: ['organization_id' => $organizationId],
        );
        if (!$response instanceof PlatformResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Platform::convertToPlatform.');
        }
        return $response;
    }

    public function createPlatformUser(CreatePlatformUserRequest $request): PlatformResponse
    {
        $response = $this->execute(PlatformEndpoints::createPlatformUser(), request: $request);
        if (!$response instanceof PlatformResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Platform::createPlatformUser.');
        }
        return $response;
    }
}
