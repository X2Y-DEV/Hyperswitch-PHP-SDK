<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\MerchantAccount\Request\CreateMerchantAccountRequest;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Request\UpdateMerchantAccountRequest;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Response\MerchantAccountDeleteResponse;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Response\MerchantAccountResponse;
use X2Y\HyperSwitchClient\Endpoint\MerchantAccountsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Merchant Accounts group. Admin-tier.
 *
 * @postman             MerchantAccounts
 * @hyperswitch-source  2026-04-30
 */
final class MerchantAccountsResource extends AbstractResource
{
    public function create(CreateMerchantAccountRequest $request): MerchantAccountResponse
    {
        $response = $this->execute(
            MerchantAccountsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof MerchantAccountResponse) {
            throw new HyperSwitchApiException('Unexpected response type from MerchantAccounts::create.');
        }
        return $response;
    }

    public function retrieve(string $merchantId): MerchantAccountResponse
    {
        $response = $this->execute(
            MerchantAccountsEndpoints::retrieve(),
            pathParams: ['id' => $merchantId],
        );
        if (!$response instanceof MerchantAccountResponse) {
            throw new HyperSwitchApiException('Unexpected response type from MerchantAccounts::retrieve.');
        }
        return $response;
    }

    public function update(string $merchantId, UpdateMerchantAccountRequest $request): MerchantAccountResponse
    {
        $response = $this->execute(
            MerchantAccountsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $merchantId],
        );
        if (!$response instanceof MerchantAccountResponse) {
            throw new HyperSwitchApiException('Unexpected response type from MerchantAccounts::update.');
        }
        return $response;
    }

    public function delete(string $merchantId): MerchantAccountDeleteResponse
    {
        $response = $this->execute(
            MerchantAccountsEndpoints::delete(),
            pathParams: ['id' => $merchantId],
        );
        if (!$response instanceof MerchantAccountDeleteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from MerchantAccounts::delete.');
        }
        return $response;
    }
}
