<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\BusinessProfile\Request\CreateBusinessProfileRequest;
use X2Y\HyperswitchClient\Dto\BusinessProfile\Request\UpdateBusinessProfileRequest;
use X2Y\HyperswitchClient\Dto\BusinessProfile\Response\BusinessProfileDeleteResponse;
use X2Y\HyperswitchClient\Dto\BusinessProfile\Response\BusinessProfileResponse;
use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Endpoint\BusinessProfileEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * Typed facade over the Hyperswitch Business Profile group. Admin-tier.
 *
 * @postman             Business Profile
 * @hyperswitch-source  2026-04-30
 */
final class BusinessProfileResource extends AbstractResource
{
    public function create(string $merchantId, CreateBusinessProfileRequest $request): BusinessProfileResponse
    {
        $response = $this->execute(
            BusinessProfileEndpoints::create(),
            request:    $request,
            pathParams: ['merchant_id' => $merchantId],
        );
        if (!$response instanceof BusinessProfileResponse) {
            throw new HyperswitchApiException('Unexpected response type from BusinessProfile::create.');
        }
        return $response;
    }

    public function retrieve(string $merchantId, string $profileId): BusinessProfileResponse
    {
        $response = $this->execute(
            BusinessProfileEndpoints::retrieve(),
            pathParams: ['merchant_id' => $merchantId, 'profile_id' => $profileId],
        );
        if (!$response instanceof BusinessProfileResponse) {
            throw new HyperswitchApiException('Unexpected response type from BusinessProfile::retrieve.');
        }
        return $response;
    }

    public function update(
        string $merchantId,
        string $profileId,
        UpdateBusinessProfileRequest $request,
    ): BusinessProfileResponse {
        $response = $this->execute(
            BusinessProfileEndpoints::update(),
            request:    $request,
            pathParams: ['merchant_id' => $merchantId, 'profile_id' => $profileId],
        );
        if (!$response instanceof BusinessProfileResponse) {
            throw new HyperswitchApiException('Unexpected response type from BusinessProfile::update.');
        }
        return $response;
    }

    public function delete(string $merchantId, string $profileId): BusinessProfileDeleteResponse
    {
        $response = $this->execute(
            BusinessProfileEndpoints::delete(),
            pathParams: ['merchant_id' => $merchantId, 'profile_id' => $profileId],
        );
        if (!$response instanceof BusinessProfileDeleteResponse) {
            throw new HyperswitchApiException('Unexpected response type from BusinessProfile::delete.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<BusinessProfileResponse>
     */
    public function list(string $merchantId): PaginatedList
    {
        $response = $this->execute(
            BusinessProfileEndpoints::list(),
            pathParams: ['merchant_id' => $merchantId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from BusinessProfile::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): BusinessProfileResponse => BusinessProfileResponse::fromArray($row),
        );
    }
}
