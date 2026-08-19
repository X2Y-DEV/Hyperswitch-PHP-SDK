<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\ConfirmSessionRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\CreateSessionRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\SessionTokenLookupRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\UpdateSavedPaymentMethodRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Response\SessionResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch V1 Payment Method Sessions group.
 *
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodSessionsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @endpoint  POST /v1/payment-method-sessions
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/v1/payment-method-sessions',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    true,
            requestClass:  CreateSessionRequest::class,
            responseClass: SessionResponse::class,
        );
    }

    /**
     * @endpoint  GET /v1/payment-method-sessions/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/v1/payment-method-sessions/{id}',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  null,
            responseClass: SessionResponse::class,
        );
    }

    /**
     * @endpoint  GET /v1/payment-method-sessions/{id}/list-payment-methods
     *
     * Returns the available payment methods for the session — Resource
     * passes through the raw envelope as PaginatedList<array> until
     * HyperSwitch publishes a stable shape for this endpoint.
     */
    public static function listPaymentMethods(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/v1/payment-method-sessions/{id}/list-payment-methods',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @endpoint  PUT /v1/payment-method-sessions/{id}/update-saved-payment-method
     */
    public static function updateSavedPaymentMethod(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/v1/payment-method-sessions/{id}/update-saved-payment-method',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  UpdateSavedPaymentMethodRequest::class,
            responseClass: SessionResponse::class,
        );
    }

    /**
     * @endpoint  POST /v1/payment-method-sessions/{id}/confirm
     */
    public static function confirm(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/v1/payment-method-sessions/{id}/confirm',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    true,
            requestClass:  ConfirmSessionRequest::class,
            responseClass: SessionResponse::class,
        );
    }

    /**
     * @endpoint  DELETE /v1/payment-method-sessions/{token}
     *
     * Despite the DELETE verb, this is effectively a token-lookup /
     * revocation operation that returns the underlying payment method id.
     * HyperSwitch's choice of HTTP method is unusual but modelled exactly.
     */
    public static function tokenLookup(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/v1/payment-method-sessions/{token}',
            authTier:      AuthTier::MERCHANT_V1,
            idempotent:    false,
            requestClass:  SessionTokenLookupRequest::class,
            responseClass: SessionResponse::class,
        );
    }
}
