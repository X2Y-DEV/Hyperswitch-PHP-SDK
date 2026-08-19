<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request\ProfileAcquirerCreateRequest;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request\ProfileAcquirerUpdateRequest;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Response\ProfileAcquirerResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Profile Acquirer group.
 *
 * @openapi             tag: Profile Acquirer
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ProfileAcquirerEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "Create a Profile Acquirer" @endpoint POST /profile_acquirers */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/profile_acquirers',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  ProfileAcquirerCreateRequest::class,
            responseClass: ProfileAcquirerResponse::class,
        );
    }

    /** @openapi "Update a Profile Acquirer" @endpoint POST /profile_acquirers/{profile_id}/{profile_acquirer_id} */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/profile_acquirers/{profile_id}/{profile_acquirer_id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  ProfileAcquirerUpdateRequest::class,
            responseClass: ProfileAcquirerResponse::class,
        );
    }
}
