<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodDeleteResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request\CreatePaymentMethodV1Request;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request\UpdatePaymentMethodV1Request;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Response\CustomerDefaultPaymentMethodResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch v1 payment-methods surface (`/payment_methods`, `/account/…`,
 * `/customers/…/payment_methods`). Distinct from PaymentMethodsEndpoints, which
 * wraps the v2-compat modular `/v1/payment-methods` flow.
 *
 * @openapi             tag: Payment Methods
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PaymentMethodsV1Endpoints
{
    private function __construct()
    {
    }

    /**
     * @openapi   "Create a Payment Method"
     * @endpoint  POST /payment_methods
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payment_methods',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreatePaymentMethodV1Request::class,
            responseClass: PaymentMethodResponse::class,
        );
    }

    /**
     * @openapi   "Retrieve a Payment method"
     * @endpoint  GET /payment_methods/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payment_methods/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentMethodResponse::class,
        );
    }

    /**
     * @openapi   "Update a Payment method"
     * @endpoint  POST /payment_methods/{id}/update
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payment_methods/{id}/update',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdatePaymentMethodV1Request::class,
            responseClass: PaymentMethodResponse::class,
        );
    }

    /**
     * @openapi   "Delete a Payment method"
     * @endpoint  DELETE /payment_methods/{id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/payment_methods/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentMethodDeleteResponse::class,
        );
    }

    /**
     * @openapi   "List all Payment Methods for a Merchant"
     * @endpoint  GET /account/payment_methods
     *
     * Returns a merchant payment-methods list; the Resource returns it raw.
     */
    public static function listForMerchant(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/account/payment_methods',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @openapi   "List Customer Payment Methods via Client Secret"
     * @endpoint  GET /customers/payment_methods
     */
    public static function listForCustomerViaClientSecret(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/customers/payment_methods',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @openapi   "List all Payment Methods for a Customer"
     * @endpoint  GET /customers/{id}/payment_methods
     */
    public static function listForCustomer(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/customers/{id}/payment_methods',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @openapi   "List Payment Methods for a Payment via Client SDK"
     * @endpoint  GET /payments/{id}/client
     */
    public static function listForPaymentClient(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payments/{id}/client',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @openapi   "Set the Payment Method as Default"
     * @endpoint  POST /{customer_id}/payment_methods/{payment_method_id}/default
     */
    public static function setDefault(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/{customer_id}/payment_methods/{payment_method_id}/default',
            authTier:      AuthTier::EPHEMERAL,
            idempotent:    false,
            requestClass:  null,
            responseClass: CustomerDefaultPaymentMethodResponse::class,
        );
    }
}
