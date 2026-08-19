<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Blocklist\Request\BlocklistRequest;
use X2Y\HyperSwitchClient\Dto\Blocklist\Response\BlocklistResponse;
use X2Y\HyperSwitchClient\Dto\Blocklist\Response\ToggleBlocklistResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Blocklist group. All merchant (api_key).
 *
 * @openapi             tag: Blocklist
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class BlocklistEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "Block a Fingerprint" @endpoint POST /blocklist */
    public static function block(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/blocklist',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  BlocklistRequest::class,
            responseClass: BlocklistResponse::class,
        );
    }

    /** @openapi "Unblock a Fingerprint" @endpoint DELETE /blocklist */
    public static function unblock(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/blocklist',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  BlocklistRequest::class,
            responseClass: BlocklistResponse::class,
        );
    }

    /** @openapi "List Blocked fingerprints of a particular kind" @endpoint GET /blocklist */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/blocklist',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /** @openapi "List batch blocklist jobs" @endpoint GET /blocklist/batch */
    public static function listBatch(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/blocklist/batch',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /** @openapi "Get batch blocklist job status" @endpoint GET /blocklist/batch/{id} */
    public static function batchStatus(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/blocklist/batch/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /** @openapi "Upload a batch blocklist CSV" @endpoint POST /blocklist/batch */
    public static function uploadBatch(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/blocklist/batch',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /** @openapi "Toggle blocklist guard for a particular merchant" @endpoint POST /blocklist/toggle */
    public static function toggle(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/blocklist/toggle',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: ToggleBlocklistResponse::class,
        );
    }
}
