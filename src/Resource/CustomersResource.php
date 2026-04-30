<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Customer\Request\CreateCustomerRequest;
use X2Y\HyperswitchClient\Dto\Customer\Request\CreateEphemeralKeyRequest;
use X2Y\HyperswitchClient\Dto\Customer\Request\UpdateCustomerRequest;
use X2Y\HyperswitchClient\Dto\Customer\Response\CustomerDeleteResponse;
use X2Y\HyperswitchClient\Dto\Customer\Response\CustomerResponse;
use X2Y\HyperswitchClient\Dto\Customer\Response\EphemeralKeyResponse;
use X2Y\HyperswitchClient\Endpoint\CustomersEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;

/**
 * Typed facade over the Hyperswitch Customers API group.
 *
 * @postman             Customers
 * @hyperswitch-source  2026-04-30
 */
final class CustomersResource implements ResourceInterface
{
    public function __construct(private readonly ApiClientInterface $client)
    {
    }

    /**
     * @postman   Customers / Create Customer
     */
    public function create(CreateCustomerRequest $request): CustomerResponse
    {
        $response = $this->client->execute(
            CustomersEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof CustomerResponse) {
            throw new HyperswitchApiException('Unexpected response type from Customers::create.');
        }
        return $response;
    }

    /**
     * @postman   Customers / Retrieve Customer
     */
    public function retrieve(string $customerId): CustomerResponse
    {
        $response = $this->client->execute(
            CustomersEndpoints::retrieve(),
            pathParams: ['id' => $customerId],
        );
        if (!$response instanceof CustomerResponse) {
            throw new HyperswitchApiException('Unexpected response type from Customers::retrieve.');
        }
        return $response;
    }

    /**
     * @postman   Customers / Update Customer
     */
    public function update(string $customerId, UpdateCustomerRequest $request): CustomerResponse
    {
        $response = $this->client->execute(
            CustomersEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $customerId],
        );
        if (!$response instanceof CustomerResponse) {
            throw new HyperswitchApiException('Unexpected response type from Customers::update.');
        }
        return $response;
    }

    /**
     * @postman   Customers / Delete Customer
     */
    public function delete(string $customerId): CustomerDeleteResponse
    {
        $response = $this->client->execute(
            CustomersEndpoints::delete(),
            pathParams: ['id' => $customerId],
        );
        if (!$response instanceof CustomerDeleteResponse) {
            throw new HyperswitchApiException('Unexpected response type from Customers::delete.');
        }
        return $response;
    }

    /**
     * @postman   Customers / List Customer
     *
     * @return PaginatedList<CustomerResponse>
     */
    public function list(): PaginatedList
    {
        $response = $this->client->execute(CustomersEndpoints::list());
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from Customers::list.');
        }
        /** @var array<string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): CustomerResponse => CustomerResponse::fromArray($row),
        );
    }

    /**
     * @postman   Customers / Ephemeral Key
     *
     * Mints a short-lived ephemeral key bound to the given customer. The
     * resulting `secret` is what the merchant hands to client-side code.
     */
    public function createEphemeralKey(CreateEphemeralKeyRequest $request): EphemeralKeyResponse
    {
        $response = $this->client->execute(
            CustomersEndpoints::ephemeralKey(),
            request: $request,
        );
        if (!$response instanceof EphemeralKeyResponse) {
            throw new HyperswitchApiException('Unexpected response type from Customers::createEphemeralKey.');
        }
        return $response;
    }
}
