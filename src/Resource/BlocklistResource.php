<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Blocklist\Request\BlocklistRequest;
use X2Y\HyperSwitchClient\Dto\Blocklist\Response\BlocklistResponse;
use X2Y\HyperSwitchClient\Dto\Blocklist\Response\ToggleBlocklistResponse;
use X2Y\HyperSwitchClient\Endpoint\BlocklistEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * @openapi             tag: Blocklist
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class BlocklistResource extends AbstractResource
{
    /** @openapi "Block a Fingerprint" */
    public function block(BlocklistRequest $request): BlocklistResponse
    {
        $response = $this->execute(BlocklistEndpoints::block(), request: $request);
        return $this->asBlocklist($response, 'block');
    }

    /** @openapi "Unblock a Fingerprint" */
    public function unblock(BlocklistRequest $request): BlocklistResponse
    {
        $response = $this->execute(BlocklistEndpoints::unblock(), request: $request);
        return $this->asBlocklist($response, 'unblock');
    }

    /**
     * @openapi "List Blocked fingerprints of a particular kind"
     *
     * @param array<string, scalar> $queryParams e.g. ['data_kind' => 'card_bin'].
     * @return array<int|string, mixed>
     */
    public function list(array $queryParams = []): array
    {
        return $this->raw(BlocklistEndpoints::list(), 'list', $queryParams);
    }

    /**
     * @openapi "List batch blocklist jobs"
     *
     * @return array<int|string, mixed>
     */
    public function listBatch(): array
    {
        return $this->raw(BlocklistEndpoints::listBatch(), 'listBatch');
    }

    /**
     * @openapi "Get batch blocklist job status"
     *
     * @return array<int|string, mixed>
     */
    public function batchStatus(string $jobId): array
    {
        return $this->raw(BlocklistEndpoints::batchStatus(), 'batchStatus', [], ['id' => $jobId]);
    }

    /**
     * @openapi "Upload a batch blocklist CSV"
     *
     * @return array<int|string, mixed>
     */
    public function uploadBatch(): array
    {
        return $this->raw(BlocklistEndpoints::uploadBatch(), 'uploadBatch');
    }

    /**
     * @openapi "Toggle blocklist guard for a particular merchant"
     *
     * @param array<string, scalar> $queryParams e.g. ['status' => 'true'].
     */
    public function toggle(array $queryParams = []): ToggleBlocklistResponse
    {
        $response = $this->execute(BlocklistEndpoints::toggle(), queryParams: $queryParams);
        if (!$response instanceof ToggleBlocklistResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Blocklist::toggle.');
        }
        return $response;
    }

    private function asBlocklist(mixed $response, string $context): BlocklistResponse
    {
        if (!$response instanceof BlocklistResponse) {
            throw new HyperSwitchApiException("Unexpected response type from Blocklist::{$context}.");
        }
        return $response;
    }

    /**
     * @param array<string, scalar> $queryParams
     * @param array<string, string> $pathParams
     * @return array<int|string, mixed>
     */
    private function raw(Endpoint $endpoint, string $context, array $queryParams = [], array $pathParams = []): array
    {
        $response = $this->execute($endpoint, pathParams: $pathParams, queryParams: $queryParams);
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException("Unexpected response type from Blocklist::{$context}.");
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return $raw;
    }
}
