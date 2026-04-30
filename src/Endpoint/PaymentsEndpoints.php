<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Endpoint;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\Payment\Request\CancelPaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\CancelPostCapturePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\CapturePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\ConfirmPaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\IncrementalAuthorizationRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\SessionTokensRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\UpdatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Dto\Payment\Response\SessionTokensResponse;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * Hyperswitch Payments group. The hot path of the SDK — every storefront
 * checkout flow runs through here.
 *
 * `POST /payments` is polymorphic: the same path handles standard creates,
 * CIT/MIT mandate flows, setup-mandate, saved-card, and payment-link
 * creates. The Resource exposes a single `create()` method; consumers
 * populate `CreatePaymentRequest` differently per intent.
 *
 * Similarly, `POST /payments/{id}/confirm` accepts both rich confirm and
 * confirm-with-token shapes — same Endpoint, one Request DTO with optional
 * fields (clientSecret, paymentToken, cardCvc).
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
     * @postman   Payments / Payments - Update
     * @endpoint  POST /payments/{id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdatePaymentRequest::class,
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

    /**
     * @postman   Payments / Payments - Confirm
     * @postman   Payments / Payments - Confirm with token
     * @endpoint  POST /payments/{id}/confirm
     *
     * Auth tier is PUBLISHABLE — confirm can be called from the client side
     * with the publishable key once the intent has been created server-side.
     */
    public static function confirm(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/confirm',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    true,
            requestClass:  ConfirmPaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @postman   Payments / Payments - Capture
     * @endpoint  POST /payments/{id}/capture
     */
    public static function capture(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/capture',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CapturePaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @postman   Payments / Payments - Cancel
     * @endpoint  POST /payments/{id}/cancel
     */
    public static function cancel(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/cancel',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  CancelPaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @postman   Payments / Payments - Cancel Post Capture
     * @endpoint  POST /payments/{id}/cancel_post_capture
     */
    public static function cancelPostCapture(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/cancel_post_capture',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  CancelPostCapturePaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @postman   Payments / Payment-List
     * @endpoint  GET /payments/list
     *
     * Resource wraps in PaginatedList<PaymentResponse>. Filters travel as
     * query parameters via ListPaymentsRequest::toArray().
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payments/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Payments / Session Token
     * @endpoint  POST /payments/session_tokens
     *
     * Auth tier is PUBLISHABLE — minted client-side for wallet flows.
     */
    public static function sessionTokens(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/session_tokens',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    true,
            requestClass:  SessionTokensRequest::class,
            responseClass: SessionTokensResponse::class,
        );
    }

    /**
     * @postman   Payments / Payments - Incremental Authorization
     * @endpoint  POST /payments/{id}/incremental_authorization
     */
    public static function incrementalAuthorization(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/incremental_authorization',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  IncrementalAuthorizationRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @postman   Payments / Payment - Extend Authorization
     * @endpoint  POST /payments/{id}/extend_authorization
     */
    public static function extendAuthorization(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/extend_authorization',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentResponse::class,
        );
    }
}
