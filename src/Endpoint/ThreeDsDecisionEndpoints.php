<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Request\ThreeDsDecisionExecuteRequest;
use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Response\ThreeDsDecisionExecuteResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch 3DS Decision Rule group.
 *
 * @openapi             tag: 3DS Decision Rule
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ThreeDsDecisionEndpoints
{
    private function __construct()
    {
    }

    /**
     * @openapi   "Execute 3DS Decision Rule"
     * @endpoint  POST /three_ds_decision/execute
     */
    public static function execute(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/three_ds_decision/execute',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  ThreeDsDecisionExecuteRequest::class,
            responseClass: ThreeDsDecisionExecuteResponse::class,
        );
    }
}
