<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\ApiKey\Request\CreateApiKeyRequest;
use X2Y\HyperSwitchClient\Dto\ApiKey\Request\UpdateApiKeyRequest;
use X2Y\HyperSwitchClient\Dto\ApiKey\Response\ApiKeyDeleteResponse;
use X2Y\HyperSwitchClient\Dto\ApiKey\Response\ApiKeyResponse;
use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Endpoint\ApiKeysEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch API Keys group. Admin-tier.
 *
 * @postman             API Key
 * @hyperswitch-source  2026-04-30
 */
final class ApiKeysResource extends AbstractResource
{
    public function create(string $merchantId, CreateApiKeyRequest $request): ApiKeyResponse
    {
        $response = $this->execute(
            ApiKeysEndpoints::create(),
            request:    $request,
            pathParams: ['merchant_id' => $merchantId],
        );
        if (!$response instanceof ApiKeyResponse) {
            throw new HyperSwitchApiException('Unexpected response type from ApiKeys::create.');
        }
        return $response;
    }

    public function update(string $merchantId, string $apiKeyId, UpdateApiKeyRequest $request): ApiKeyResponse
    {
        $response = $this->execute(
            ApiKeysEndpoints::update(),
            request:    $request,
            pathParams: ['merchant_id' => $merchantId, 'api_key_id' => $apiKeyId],
        );
        if (!$response instanceof ApiKeyResponse) {
            throw new HyperSwitchApiException('Unexpected response type from ApiKeys::update.');
        }
        return $response;
    }

    public function retrieve(string $merchantId, string $apiKeyId): ApiKeyResponse
    {
        $response = $this->execute(
            ApiKeysEndpoints::retrieve(),
            pathParams: ['merchant_id' => $merchantId, 'api_key_id' => $apiKeyId],
        );
        if (!$response instanceof ApiKeyResponse) {
            throw new HyperSwitchApiException('Unexpected response type from ApiKeys::retrieve.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<ApiKeyResponse>
     */
    public function list(string $merchantId): PaginatedList
    {
        $response = $this->execute(
            ApiKeysEndpoints::list(),
            pathParams: ['merchant_id' => $merchantId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from ApiKeys::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): ApiKeyResponse => ApiKeyResponse::fromArray($row),
        );
    }

    public function delete(string $merchantId, string $apiKeyId): ApiKeyDeleteResponse
    {
        $response = $this->execute(
            ApiKeysEndpoints::delete(),
            pathParams: ['merchant_id' => $merchantId, 'api_key_id' => $apiKeyId],
        );
        if (!$response instanceof ApiKeyDeleteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from ApiKeys::delete.');
        }
        return $response;
    }
}
