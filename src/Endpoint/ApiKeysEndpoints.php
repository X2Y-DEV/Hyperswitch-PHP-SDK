<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\ApiKey\Request\CreateApiKeyRequest;
use X2Y\HyperSwitchClient\Dto\ApiKey\Request\UpdateApiKeyRequest;
use X2Y\HyperSwitchClient\Dto\ApiKey\Response\ApiKeyDeleteResponse;
use X2Y\HyperSwitchClient\Dto\ApiKey\Response\ApiKeyResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch API Key management. Admin-tier — API key lifecycle is
 * managed with the platform's admin key, not a merchant key.
 *
 * @hyperswitch-source  2026-04-30
 */
final class ApiKeysEndpoints
{
    private function __construct()
    {
    }

    /**
     * @endpoint  POST /api_keys/{merchant_id}
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/api_keys/{merchant_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    true,
            requestClass:  CreateApiKeyRequest::class,
            responseClass: ApiKeyResponse::class,
        );
    }

    /**
     * @endpoint  POST /api_keys/{merchant_id}/{api_key_id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/api_keys/{merchant_id}/{api_key_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  UpdateApiKeyRequest::class,
            responseClass: ApiKeyResponse::class,
        );
    }

    /**
     * @endpoint  GET /api_keys/{merchant_id}/{api_key_id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/api_keys/{merchant_id}/{api_key_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: ApiKeyResponse::class,
        );
    }

    /**
     * @endpoint  GET /api_keys/{merchant_id}/list
     *
     * Resource wraps in PaginatedList<ApiKeyResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/api_keys/{merchant_id}/list',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @endpoint  DELETE /api_keys/{merchant_id}/{api_key_id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/api_keys/{merchant_id}/{api_key_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: ApiKeyDeleteResponse::class,
        );
    }
}
