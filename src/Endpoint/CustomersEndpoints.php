<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Customer\Request\CreateCustomerRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Request\CreateEphemeralKeyRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Request\UpdateCustomerRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Response\CustomerDeleteResponse;
use X2Y\HyperSwitchClient\Dto\Customer\Response\CustomerResponse;
use X2Y\HyperSwitchClient\Dto\Customer\Response\EphemeralKeyResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Customers group.
 *
 * @postman             Customers
 * @hyperswitch-source  2026-04-30
 */
final class CustomersEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Customers / Create Customer
     * @endpoint  POST /customers
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/customers',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreateCustomerRequest::class,
            responseClass: CustomerResponse::class,
        );
    }

    /**
     * @postman   Customers / Retrieve Customer
     * @endpoint  GET /customers/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/customers/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: CustomerResponse::class,
        );
    }

    /**
     * @postman   Customers / Update Customer
     * @endpoint  POST /customers/{id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/customers/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateCustomerRequest::class,
            responseClass: CustomerResponse::class,
        );
    }

    /**
     * @postman   Customers / Delete Customer
     * @endpoint  DELETE /customers/{id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/customers/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: CustomerDeleteResponse::class,
        );
    }

    /**
     * @postman   Customers / List Customer
     * @endpoint  GET /customers/list
     *
     * Returns a list envelope; Resource wraps it in PaginatedList<CustomerResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/customers/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Customers / Ephemeral Key
     * @endpoint  POST /ephemeral_keys
     *
     * Note: the path is /ephemeral_keys (not /customers/...). Authenticated as
     * MERCHANT — the endpoint *creates* an ephemeral key; it is not consumed
     * with one.
     */
    public static function ephemeralKey(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/ephemeral_keys',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreateEphemeralKeyRequest::class,
            responseClass: EphemeralKeyResponse::class,
        );
    }
}
