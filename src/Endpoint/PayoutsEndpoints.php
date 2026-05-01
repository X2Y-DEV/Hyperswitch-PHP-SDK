<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CancelPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\ConfirmPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CreatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\UpdatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Payouts group.
 *
 * The Postman "Payout Links" group is intentionally not modelled as its
 * own Resource because all five of its items collide with paths owned by
 * Payouts (POST /payouts/create, /confirm, /fulfill, GET /payouts/{id})
 * or by BusinessProfile (POST /account/{merchant_id}/business_profile/{profile_id}).
 * Payout-link flows are activated by setting `payout_link: true` on
 * `CreatePayoutRequest` — same Endpoint, different body.
 *
 * @postman             Payouts
 * @hyperswitch-source  2026-04-30
 */
final class PayoutsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Payouts / Payouts - Create
     * @postman   Payout Links / Payout Links - Create
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
     * @postman   Payouts / Payouts - Update
     * @endpoint  PUT /payouts/{id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/payouts/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdatePayoutRequest::class,
            responseClass: PayoutResponse::class,
        );
    }

    /**
     * @postman   Payouts / Payouts - Confirm
     * @postman   Payout Links / Payout Links - Confirm
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
     * @postman   Payouts / Payouts - Fulfill
     * @postman   Payout Links / Payout Links - Fulfill
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
     * @postman   Payouts / Payouts - Cancel
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
     * @postman   Payouts / Payouts - Retrieve
     * @postman   Payout Links / Payout Links - Retrieve
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
}
