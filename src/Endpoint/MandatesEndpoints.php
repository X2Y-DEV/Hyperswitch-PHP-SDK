<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Mandate\Response\MandateResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Mandates group.
 *
 * Note: 7 of the Postman items in this group hit `POST /payments` with
 * different bodies (CIT, Setup-Mandate, MIT-by-payment-method-id, MIT-by-
 * mandate-id, MIT-by-network-tx-id, MIT-by-processor-token, MIT-by-
 * limited-card-data). These are not separate Endpoints — they are
 * polymorphic body shapes of {@see PaymentsEndpoints::create()}.
 * Consumers populate `CreatePaymentRequest` differently per intent.
 *
 * Only the three genuinely-distinct mandate paths are modelled here.
 *
 * @postman             Mandates
 * @hyperswitch-source  2026-04-30
 */
final class MandatesEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Mandates / Mandate - List details of a mandate
     * @endpoint  GET /mandates/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/mandates/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: MandateResponse::class,
        );
    }

    /**
     * @postman   Mandates / Mandate - List all mandates against a customer id
     * @endpoint  GET /customers/{customer_id}/mandates
     *
     * Resource wraps in PaginatedList<MandateResponse>.
     */
    public static function listForCustomer(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/customers/{customer_id}/mandates',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Mandates / Mandate - Revoke a mandate
     * @endpoint  POST /mandates/revoke/{id}
     */
    public static function revoke(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/mandates/revoke/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: MandateResponse::class,
        );
    }
}
