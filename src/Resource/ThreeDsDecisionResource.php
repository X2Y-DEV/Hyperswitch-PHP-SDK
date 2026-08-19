<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Request\ThreeDsDecisionExecuteRequest;
use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Response\ThreeDsDecisionExecuteResponse;
use X2Y\HyperSwitchClient\Endpoint\ThreeDsDecisionEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * @openapi             tag: 3DS Decision Rule
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ThreeDsDecisionResource extends AbstractResource
{
    /**
     * @openapi   "Execute 3DS Decision Rule"
     */
    public function executeRule(ThreeDsDecisionExecuteRequest $request): ThreeDsDecisionExecuteResponse
    {
        $response = $this->execute(ThreeDsDecisionEndpoints::execute(), request: $request);
        if (!$response instanceof ThreeDsDecisionExecuteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from ThreeDsDecision::executeRule.');
        }
        return $response;
    }
}
