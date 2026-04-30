<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Endpoint;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\Relay\Request\CreateRelayRequest;
use X2Y\HyperswitchClient\Dto\Relay\Response\RelayResponse;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * Hyperswitch Relay group. Three Postman items (capture / void /
 * incremental-auth) all hit POST /relay with different `data` payloads —
 * single Endpoint, one permissive `CreateRelayRequest`.
 *
 * The Postman "Relay - Retrieve" item has an empty URL. Modelled
 * speculatively as `GET /relay/{id}` based on Hyperswitch's conventional
 * shape; verify against an updated Postman / OpenAPI export when available.
 *
 * @postman             Relay
 * @hyperswitch-source  2026-04-30
 */
final class RelayEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Relay / Relay - Create
     * @postman   Relay / Relay Create Incremental Auth
     * @postman   Relay / Relay Create Void
     * @endpoint  POST /relay
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/relay',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreateRelayRequest::class,
            responseClass: RelayResponse::class,
        );
    }

    /**
     * @postman   Relay / Relay - Retrieve  (Postman URL was blank)
     * @endpoint  GET /relay/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/relay/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: RelayResponse::class,
        );
    }
}
