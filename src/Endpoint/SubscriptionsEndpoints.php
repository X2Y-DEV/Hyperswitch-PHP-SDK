<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\CancelSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\ConfirmSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\CreateAndConfirmSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\CreateSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\PauseSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\ResumeSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\UpdateSubscriptionRequest;
use X2Y\HyperSwitchClient\Dto\Subscription\Response\EstimateResponse;
use X2Y\HyperSwitchClient\Dto\Subscription\Response\SubscriptionResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Subscriptions group.
 *
 * Two distinct creation flows:
 *   - POST /subscriptions/create  — create only, must be confirmed separately
 *   - POST /subscriptions         — create + confirm in one call
 *
 * @hyperswitch-source  2026-04-30
 */
final class SubscriptionsEndpoints
{
    private function __construct()
    {
    }

    /**
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

    /**
     * @openapi   "Get Subscription Items"
     * @endpoint  GET /subscriptions/items
     *
     * Returns a bare array of subscription items; the Resource returns them raw.
     */
    public static function items(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/subscriptions/items',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }
}
