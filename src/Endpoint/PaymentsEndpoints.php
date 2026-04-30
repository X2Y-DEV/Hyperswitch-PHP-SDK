<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Endpoint;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * Hyperswitch Payments group.
 *
 * Stub set — `create` and `retrieve` populated as the template. AI maintenance
 * fills the remaining endpoints (update, confirm, capture, cancel, list,
 * session_tokens, incremental_authorization, extend_authorization).
 *
 * @postman             Payments
 * @hyperswitch-source  2026-04-30
 */
final class PaymentsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Payments / Payments - Create
     * @endpoint  POST /payments
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreatePaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @postman   Payments / Payments - Retrieve
     * @endpoint  GET /payments/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payments/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentResponse::class,
        );
    }
}
