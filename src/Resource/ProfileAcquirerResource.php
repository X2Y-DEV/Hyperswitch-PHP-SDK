<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request\ProfileAcquirerCreateRequest;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request\ProfileAcquirerUpdateRequest;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Response\ProfileAcquirerResponse;
use X2Y\HyperSwitchClient\Endpoint\ProfileAcquirerEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * @openapi             tag: Profile Acquirer
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ProfileAcquirerResource extends AbstractResource
{
    /** @openapi "Create a Profile Acquirer" */
    public function create(ProfileAcquirerCreateRequest $request): ProfileAcquirerResponse
    {
        return $this->asAcquirer($this->execute(ProfileAcquirerEndpoints::create(), request: $request), 'create');
    }

    /** @openapi "Update a Profile Acquirer" */
    public function update(
        string $profileId,
        string $profileAcquirerId,
        ProfileAcquirerUpdateRequest $request,
    ): ProfileAcquirerResponse {
        $response = $this->execute(
            ProfileAcquirerEndpoints::update(),
            request:    $request,
            pathParams: ['profile_id' => $profileId, 'profile_acquirer_id' => $profileAcquirerId],
        );
        return $this->asAcquirer($response, 'update');
    }

    private function asAcquirer(mixed $response, string $context): ProfileAcquirerResponse
    {
        if (!$response instanceof ProfileAcquirerResponse) {
            throw new HyperSwitchApiException("Unexpected response type from ProfileAcquirer::{$context}.");
        }
        return $response;
    }
}
