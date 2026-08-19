<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Poll\Response\PollResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Poll group.
 *
 * @openapi             tag: Poll
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PollEndpoints
{
    private function __construct()
    {
    }

    /**
     * @openapi   "Retrieve Poll Status"
     * @endpoint  GET /poll/status/{id}
     */
    public static function status(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/poll/status/{id}',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  null,
            responseClass: PollResponse::class,
        );
    }
}
