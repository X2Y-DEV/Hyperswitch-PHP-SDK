<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CancelPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CancelPostCapturePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CapturePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CompleteAuthorizePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ConfirmPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\EligibilityCheckRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\EligibilityRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ExternalAuthenticationRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\IncrementalAuthorizationRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\PostSessionTokensRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\SessionTokensRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\UpdateMetadataPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\UpdatePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Response\EligibilityCheckResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\EligibilityResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\ExternalAuthenticationResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\PostSessionTokensResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\SessionTokensResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\UpdateMetadataPaymentResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Payments group. The hot path of the SDK — every storefront
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
 * @hyperswitch-source  2026-04-30
 */
final class PaymentsEndpoints
{
    private function __construct()
    {
    }

    /**
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

    /**
     * @openapi   "Cancel a Payment Post Capture Retrieve"
     * @endpoint  GET /payments/{id}/cancel_post_capture
     */
    public static function cancelPostCaptureRetrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payments/{id}/cancel_post_capture',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @openapi   "Complete Authorize a Payment"
     * @endpoint  POST /payments/{id}/complete_authorize
     */
    public static function completeAuthorize(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/complete_authorize',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  CompleteAuthorizePaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    /**
     * @openapi   "Initiate external authentication for a Payment"
     * @endpoint  POST /payments/{id}/3ds/authentication
     */
    public static function externalAuthentication(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/3ds/authentication',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  ExternalAuthenticationRequest::class,
            responseClass: ExternalAuthenticationResponse::class,
        );
    }

    /**
     * @openapi   "Submit Eligibility data for a Payment"
     * @endpoint  POST /payments/{id}/eligibility
     */
    public static function eligibility(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/eligibility',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  EligibilityRequest::class,
            responseClass: EligibilityResponse::class,
        );
    }

    /**
     * @openapi   "Submit Eligibility Check data for a Payment"
     * @endpoint  POST /payments/{id}/eligibility_check
     */
    public static function eligibilityCheck(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/eligibility_check',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  EligibilityCheckRequest::class,
            responseClass: EligibilityCheckResponse::class,
        );
    }

    /**
     * @openapi   "Create Post Session Tokens for a Payment"
     * @endpoint  POST /payments/{id}/post_session_tokens
     */
    public static function postSessionTokens(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/post_session_tokens',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  PostSessionTokensRequest::class,
            responseClass: PostSessionTokensResponse::class,
        );
    }

    /**
     * @openapi   "Update Metadata for a Payment"
     * @endpoint  POST /payments/{id}/update_metadata
     */
    public static function updateMetadata(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments/{id}/update_metadata',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateMetadataPaymentRequest::class,
            responseClass: UpdateMetadataPaymentResponse::class,
        );
    }
}
