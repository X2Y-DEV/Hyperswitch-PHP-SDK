<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CancelPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\ConfirmPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CreatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\FilterPayoutsRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\PayoutFiltersRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\UpdatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutListFiltersResponse;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Payouts group.
 *
 * The "Payout Links" flows are intentionally not modelled as their
 * own Resource because all five of them collide with paths owned by
 * Payouts (POST /payouts/create, /confirm, /fulfill, GET /payouts/{id})
 * or by BusinessProfile (POST /account/{merchant_id}/business_profile/{profile_id}).
 * Payout-link flows are activated by setting `payout_link: true` on
 * `CreatePayoutRequest` — same Endpoint, different body.
 *
 * @hyperswitch-source  2026-04-30
 */
final class PayoutsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @endpoint  POST /payouts/create
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/create',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreatePayoutRequest::class,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @openapi   "Update a Payout"
     * @endpoint  POST /payouts/{id}
     *
     * v1 spec uses POST (not PUT) for payout update — reconciled 2026-08-19.
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdatePayoutRequest::class,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @endpoint  POST /payouts/{id}/confirm
     */
    public static function confirm(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/{id}/confirm',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  ConfirmPayoutRequest::class,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @endpoint  POST /payouts/{id}/fulfill
     */
    public static function fulfill(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/{id}/fulfill',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @endpoint  POST /payouts/{id}/cancel
     */
    public static function cancel(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/{id}/cancel',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  CancelPayoutRequest::class,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @endpoint  GET /payouts/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payouts/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @openapi   "List payouts using generic constraints"
     * @endpoint  GET /payouts/list
     *
     * Resource wraps in PaginatedList<PayoutResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payouts/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @openapi   "Filter payouts using specific constraints"
     * @endpoint  POST /payouts/list
     *
     * Resource wraps in PaginatedList<PayoutResponse>.
     */
    public static function filterList(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  FilterPayoutsRequest::class,
            responseClass: null,
        );
    }

    /**
     * @openapi   "List available payout filters"
     * @endpoint  POST /payouts/filter
     */
    public static function filters(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payouts/filter',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  PayoutFiltersRequest::class,
            responseClass: PayoutListFiltersResponse::class,
        );
    }
}
