<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Request\CreateMerchantAccountRequest;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Request\UpdateMerchantAccountRequest;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Response\MerchantAccountDeleteResponse;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Response\MerchantAccountResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Merchant Accounts group. Admin-tier — for onboarding new
 * merchants under the platform.
 *
 * @postman             MerchantAccounts
 * @hyperswitch-source  2026-04-30
 */
final class MerchantAccountsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   MerchantAccounts / Merchant Account - Create
     * @endpoint  POST /accounts
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/accounts',
            authTier:      AuthTier::ADMIN,
            idempotent:    true,
            requestClass:  CreateMerchantAccountRequest::class,
            responseClass: MerchantAccountResponse::class,
        );
    }

    /**
     * @postman   MerchantAccounts / Merchant Account - Retrieve
     * @endpoint  GET /accounts/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/accounts/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: MerchantAccountResponse::class,
        );
    }

    /**
     * @postman   MerchantAccounts / Merchant Account - Update
     * @endpoint  POST /accounts/{id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/accounts/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  UpdateMerchantAccountRequest::class,
            responseClass: MerchantAccountResponse::class,
        );
    }

    /**
     * @postman   MerchantAccounts / Merchant Account - Delete
     * @endpoint  DELETE /accounts/{id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/accounts/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: MerchantAccountDeleteResponse::class,
        );
    }
}
