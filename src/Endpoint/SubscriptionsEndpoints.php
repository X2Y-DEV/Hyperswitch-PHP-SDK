<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Endpoint;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\Subscription\Request\CancelSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\ConfirmSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\CreateAndConfirmSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\CreateSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\PauseSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\ResumeSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\UpdateSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Response\EstimateResponse;
use X2Y\HyperswitchClient\Dto\Subscription\Response\SubscriptionResponse;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * Hyperswitch Subscriptions group.
 *
 * Two distinct creation flows:
 *   - POST /subscriptions/create  — create only, must be confirmed separately
 *   - POST /subscriptions         — create + confirm in one call
 *
 * @postman             Subscriptions
 * @hyperswitch-source  2026-04-30
 */
final class SubscriptionsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Subscriptions / Subscription - Create
     * @endpoint  POST /subscriptions/create
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/subscriptions/create',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreateSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Create + Confirm
     * @endpoint  POST /subscriptions
     */
    public static function createAndConfirm(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/subscriptions',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreateAndConfirmSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Confirm
     * @endpoint  POST /subscriptions/{id}/confirm
     */
    public static function confirm(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/subscriptions/{id}/confirm',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  ConfirmSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Update
     * @endpoint  PUT /subscriptions/{id}/update
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/subscriptions/{id}/update',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Retrieve
     * @endpoint  GET /subscriptions/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/subscriptions/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Pause
     * @endpoint  POST /subscriptions/{id}/pause
     */
    public static function pause(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/subscriptions/{id}/pause',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  PauseSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Resume
     * @endpoint  POST /subscriptions/{id}/resume
     */
    public static function resume(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/subscriptions/{id}/resume',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  ResumeSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Cancel
     * @endpoint  POST /subscriptions/{id}/cancel
     */
    public static function cancel(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/subscriptions/{id}/cancel',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  CancelSubscriptionRequest::class,
            responseClass: SubscriptionResponse::class,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - List
     * @endpoint  GET /subscriptions/list
     *
     * Resource wraps in PaginatedList<SubscriptionResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/subscriptions/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Plans
     * @endpoint  GET /subscriptions/plans
     *
     * Resource wraps in PaginatedList<PlanResponse>.
     */
    public static function plans(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/subscriptions/plans',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Estimate
     * @endpoint  GET /subscriptions/estimate
     */
    public static function estimate(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/subscriptions/estimate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: EstimateResponse::class,
        );
    }
}
