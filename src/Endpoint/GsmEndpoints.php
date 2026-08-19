<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmCreateRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmKeyRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmUpdateRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Response\GsmDeleteResponse;
use X2Y\HyperSwitchClient\Dto\Gsm\Response\GsmResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch GSM (Global Status Mapping) group. Admin (admin_api_key).
 *
 * @openapi             tag: Gsm
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class GsmEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "Create Gsm Rule" @endpoint POST /gsm */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/gsm',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  GsmCreateRequest::class,
            responseClass: GsmResponse::class,
        );
    }

    /** @openapi "Retrieve Gsm Rule" @endpoint POST /gsm/get */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/gsm/get',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  GsmKeyRequest::class,
            responseClass: GsmResponse::class,
        );
    }

    /** @openapi "Update Gsm Rule" @endpoint POST /gsm/update */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/gsm/update',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  GsmUpdateRequest::class,
            responseClass: GsmResponse::class,
        );
    }

    /** @openapi "Delete Gsm Rule" @endpoint POST /gsm/delete */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/gsm/delete',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  GsmKeyRequest::class,
            responseClass: GsmDeleteResponse::class,
        );
    }
}
