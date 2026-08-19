<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Platform\Request\ConvertToPlatformRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Request\CreateOrganizationRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Request\CreatePlatformUserRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Request\OrganizationUpdateRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Response\OrganizationResponse;
use X2Y\HyperSwitchClient\Dto\Platform\Response\PlatformResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Platform group — admin-tier endpoints for organization /
 * multi-tenant platform onboarding.
 *
 * @postman             Platform
 * @hyperswitch-source  2026-04-30
 */
final class PlatformEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Platform / Organization - Create
     * @endpoint  POST /organization
     */
    public static function createOrganization(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/organization',
            authTier:      AuthTier::ADMIN,
            idempotent:    true,
            requestClass:  CreateOrganizationRequest::class,
            responseClass: PlatformResponse::class,
        );
    }

    /**
     * @postman   Platform / Platform - Conversion
     * @endpoint  POST /api/organization/{organization_id}/convert_to_platform
     */
    public static function convertToPlatform(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/api/organization/{organization_id}/convert_to_platform',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  ConvertToPlatformRequest::class,
            responseClass: PlatformResponse::class,
        );
    }

    /**
     * @postman   Platform / Platform - Create
     * @endpoint  POST /user/create_platform
     */
    public static function createPlatformUser(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/user/create_platform',
            authTier:      AuthTier::ADMIN,
            idempotent:    true,
            requestClass:  CreatePlatformUserRequest::class,
            responseClass: PlatformResponse::class,
        );
    }

    /**
     * @openapi   "Retrieve an Organization"
     * @endpoint  GET /organization/{id}
     */
    public static function retrieveOrganization(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/organization/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: OrganizationResponse::class,
        );
    }

    /**
     * @openapi   "Update an Organization"
     * @endpoint  PUT /organization/{id}
     */
    public static function updateOrganization(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/organization/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  OrganizationUpdateRequest::class,
            responseClass: OrganizationResponse::class,
        );
    }
}
