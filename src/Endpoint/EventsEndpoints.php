<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Event\Request\EventListConstraintsRequest;
use X2Y\HyperSwitchClient\Dto\Event\Response\EventRetrieveResponse;
use X2Y\HyperSwitchClient\Dto\Event\Response\TotalEventsResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Events (webhook delivery) group. JWT-scoped (jwt_key).
 *
 * @openapi             tag: Event
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class EventsEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "List all Events associated with a Merchant Account or Profile" @endpoint POST /events/{id} */
    public static function listForMerchant(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/events/{merchant_id}',
            authTier:      AuthTier::JWT,
            idempotent:    false,
            requestClass:  EventListConstraintsRequest::class,
            responseClass: TotalEventsResponse::class,
        );
    }

    /** @openapi "List all Events associated with a Profile" @endpoint POST /events/profile/list */
    public static function listForProfile(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/events/profile/list',
            authTier:      AuthTier::JWT,
            idempotent:    false,
            requestClass:  EventListConstraintsRequest::class,
            responseClass: TotalEventsResponse::class,
        );
    }

    /** @openapi "List all delivery attempts for an Event" @endpoint GET /events/{id}/{id}/attempts */
    public static function attempts(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/events/{merchant_id}/{event_id}/attempts',
            authTier:      AuthTier::JWT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /** @openapi "Manually retry the delivery of an Event" @endpoint POST /events/{id}/{id}/retry */
    public static function retry(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/events/{merchant_id}/{event_id}/retry',
            authTier:      AuthTier::JWT,
            idempotent:    false,
            requestClass:  null,
            responseClass: EventRetrieveResponse::class,
        );
    }
}
