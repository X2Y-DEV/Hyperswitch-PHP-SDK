<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Relay\Request\CreateRelayRequest;
use X2Y\HyperSwitchClient\Dto\Relay\Response\RelayResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Relay group. Three operations (capture / void /
 * incremental-auth) all hit POST /relay with different `data` payloads —
 * single Endpoint, one permissive `CreateRelayRequest`.
 *
 * The "Relay - Retrieve" operation has an unusual shape. Modelled
 * speculatively as `GET /relay/{id}` based on HyperSwitch's conventional
 * shape; verify against an updated v1 OpenAPI spec when available.
 *
 * @hyperswitch-source  2026-04-30
 */
final class RelayEndpoints
{
    private function __construct()
    {
    }

    /**
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
