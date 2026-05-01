<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Request\CreatePaymentMethodRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Request\UpdatePaymentMethodRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodDeleteResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodTokenDetailsResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch V1 Payment Methods (Modular Payment) group.
 *
 * Legacy `/payment_methods` endpoints are intentionally not modelled — V1
 * paths under `/v1/payment-methods` are the supported surface.
 *
 * @postman             Payment Methods - Modular Payment
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Payment Methods - Modular Payment / Payment Methods - Create
     * @endpoint  POST /v1/payment-methods
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/v1/payment-methods',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    true,
            requestClass:  CreatePaymentMethodRequest::class,
            responseClass: PaymentMethodResponse::class,
        );
    }

    /**
     * @postman   Payment Methods - Modular Payment / Payment Methods - Retrieve
     * @endpoint  GET /v1/payment-methods/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/v1/payment-methods/{id}',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentMethodResponse::class,
        );
    }

    /**
     * @postman   Payment Methods - Modular Payment / Payment Methods - Update
     * @endpoint  PUT /v1/payment-methods/{id}/update-saved-payment-method
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/v1/payment-methods/{id}/update-saved-payment-method',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  UpdatePaymentMethodRequest::class,
            responseClass: PaymentMethodResponse::class,
        );
    }

    /**
     * @postman   Payment Methods - Modular Payment / Payment Method - List Customer Saved Payment Methods
     * @endpoint  GET /v1/customers/{id}/saved-payment-methods
     *
     * Resource wraps in PaginatedList<PaymentMethodResponse>.
     */
    public static function listForCustomer(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/v1/customers/{id}/saved-payment-methods',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Payment Methods - Modular Payment / Payment Methods - Delete
     * @endpoint  DELETE /v1/payment-methods/{id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/v1/payment-methods/{id}',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentMethodDeleteResponse::class,
        );
    }

    /**
     * @postman   Payment Methods - Modular Payment / Payment Method - Get Payment Method Token Data
     * @endpoint  GET /v1/payment-methods/token/{token}/details
     */
    public static function tokenDetails(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/v1/payment-methods/token/{token}/details',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentMethodTokenDetailsResponse::class,
        );
    }
}
