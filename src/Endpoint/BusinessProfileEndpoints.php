<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\BusinessProfile\Request\CreateBusinessProfileRequest;
use X2Y\HyperSwitchClient\Dto\BusinessProfile\Request\UpdateBusinessProfileRequest;
use X2Y\HyperSwitchClient\Dto\BusinessProfile\Response\BusinessProfileDeleteResponse;
use X2Y\HyperSwitchClient\Dto\BusinessProfile\Response\BusinessProfileResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Business Profile group. Admin-tier.
 *
 * @postman             Business Profile
 * @hyperswitch-source  2026-04-30
 */
final class BusinessProfileEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Business Profile / Business Profile - Create
     * @endpoint  POST /account/{merchant_id}/business_profile
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{merchant_id}/business_profile',
            authTier:      AuthTier::ADMIN,
            idempotent:    true,
            requestClass:  CreateBusinessProfileRequest::class,
            responseClass: BusinessProfileResponse::class,
        );
    }

    /**
     * @postman   Business Profile / Business Profile - Retrieve
     * @endpoint  GET /account/{merchant_id}/business_profile/{profile_id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/account/{merchant_id}/business_profile/{profile_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: BusinessProfileResponse::class,
        );
    }

    /**
     * @postman   Business Profile / Business Profile - Update
     * @endpoint  POST /account/{merchant_id}/business_profile/{profile_id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{merchant_id}/business_profile/{profile_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  UpdateBusinessProfileRequest::class,
            responseClass: BusinessProfileResponse::class,
        );
    }

    /**
     * @postman   Business Profile / Business Profile - Delete
     * @endpoint  DELETE /account/{merchant_id}/business_profile/{profile_id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/account/{merchant_id}/business_profile/{profile_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: BusinessProfileDeleteResponse::class,
        );
    }

    /**
     * @postman   Business Profile / Business Profile - List
     * @endpoint  GET /account/{merchant_id}/business_profile
     *
     * Resource wraps in PaginatedList<BusinessProfileResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/account/{merchant_id}/business_profile',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }
}
