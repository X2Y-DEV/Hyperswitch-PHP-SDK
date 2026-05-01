<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\PaymentLink\Response\PaymentLinkResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Payment Links group.
 *
 * Note: "Create Payment Link" in the Postman collection reuses
 * `POST /payments` with `payment_link: true` in the body — there is no
 * dedicated create endpoint. Use {@see PaymentsEndpoints::create()} or
 * the `PaymentsResource::create()` facade with the `paymentLink` flag.
 *
 * @postman             Payment Links
 * @hyperswitch-source  2026-04-30
 */
final class PaymentLinksEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Payment Links / Payment Link - Retrieve
     * @endpoint  GET /payment_link/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/payment_link/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: PaymentLinkResponse::class,
        );
    }
}
