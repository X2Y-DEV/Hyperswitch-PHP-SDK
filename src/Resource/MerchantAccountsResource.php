<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\MerchantAccount\Request\CreateMerchantAccountRequest;
use X2Y\HyperswitchClient\Dto\MerchantAccount\Request\UpdateMerchantAccountRequest;
use X2Y\HyperswitchClient\Dto\MerchantAccount\Response\MerchantAccountDeleteResponse;
use X2Y\HyperswitchClient\Dto\MerchantAccount\Response\MerchantAccountResponse;
use X2Y\HyperswitchClient\Endpoint\MerchantAccountsEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;

/**
 * Typed facade over the Hyperswitch Merchant Accounts group. Admin-tier.
 *
 * @postman             MerchantAccounts
 * @hyperswitch-source  2026-04-30
 */
final class MerchantAccountsResource implements ResourceInterface
{
    public function __construct(private readonly ApiClientInterface $client)
    {
    }

    public function create(CreateMerchantAccountRequest $request): MerchantAccountResponse
    {
        $response = $this->client->execute(
            MerchantAccountsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof MerchantAccountResponse) {
            throw new HyperswitchApiException('Unexpected response type from MerchantAccounts::create.');
        }
        return $response;
    }

    public function retrieve(string $merchantId): MerchantAccountResponse
    {
        $response = $this->client->execute(
            MerchantAccountsEndpoints::retrieve(),
            pathParams: ['id' => $merchantId],
        );
        if (!$response instanceof MerchantAccountResponse) {
            throw new HyperswitchApiException('Unexpected response type from MerchantAccounts::retrieve.');
        }
        return $response;
    }

    public function update(string $merchantId, UpdateMerchantAccountRequest $request): MerchantAccountResponse
    {
        $response = $this->client->execute(
            MerchantAccountsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $merchantId],
        );
        if (!$response instanceof MerchantAccountResponse) {
            throw new HyperswitchApiException('Unexpected response type from MerchantAccounts::update.');
        }
        return $response;
    }

    public function delete(string $merchantId): MerchantAccountDeleteResponse
    {
        $response = $this->client->execute(
            MerchantAccountsEndpoints::delete(),
            pathParams: ['id' => $merchantId],
        );
        if (!$response instanceof MerchantAccountDeleteResponse) {
            throw new HyperswitchApiException('Unexpected response type from MerchantAccounts::delete.');
        }
        return $response;
    }
}
