<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Poll\Response\PollResponse;
use X2Y\HyperSwitchClient\Endpoint\PollEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * @openapi             tag: Poll
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PollResource extends AbstractResource
{
    /**
     * @openapi   "Retrieve Poll Status"
     */
    public function status(string $pollId): PollResponse
    {
        $response = $this->execute(PollEndpoints::status(), pathParams: ['id' => $pollId]);
        if (!$response instanceof PollResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Poll::status.');
        }
        return $response;
    }
}
