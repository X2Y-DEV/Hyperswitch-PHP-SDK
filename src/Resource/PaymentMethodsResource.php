<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\PaymentMethod\Request\CreatePaymentMethodRequest;
use X2Y\HyperswitchClient\Dto\PaymentMethod\Request\UpdatePaymentMethodRequest;
use X2Y\HyperswitchClient\Dto\PaymentMethod\Response\PaymentMethodDeleteResponse;
use X2Y\HyperswitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperswitchClient\Dto\PaymentMethod\Response\PaymentMethodTokenDetailsResponse;
use X2Y\HyperswitchClient\Endpoint\PaymentMethodsEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;

/**
 * Typed facade over the Hyperswitch V1 Payment Methods group.
 *
 * @postman             Payment Methods - Modular Payment
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodsResource implements ResourceInterface
{
    public function __construct(private readonly ApiClientInterface $client)
    {
    }

    public function create(CreatePaymentMethodRequest $request): PaymentMethodResponse
    {
        $response = $this->client->execute(
            PaymentMethodsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperswitchApiException('Unexpected response type from PaymentMethods::create.');
        }
        return $response;
    }

    public function retrieve(string $paymentMethodId): PaymentMethodResponse
    {
        $response = $this->client->execute(
            PaymentMethodsEndpoints::retrieve(),
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperswitchApiException('Unexpected response type from PaymentMethods::retrieve.');
        }
        return $response;
    }

    public function update(string $paymentMethodId, UpdatePaymentMethodRequest $request): PaymentMethodResponse
    {
        $response = $this->client->execute(
            PaymentMethodsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperswitchApiException('Unexpected response type from PaymentMethods::update.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<PaymentMethodResponse>
     */
    public function listForCustomer(string $customerId): PaginatedList
    {
        $response = $this->client->execute(
            PaymentMethodsEndpoints::listForCustomer(),
            pathParams: ['id' => $customerId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from PaymentMethods::listForCustomer.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): PaymentMethodResponse => PaymentMethodResponse::fromArray($row),
        );
    }

    public function delete(string $paymentMethodId): PaymentMethodDeleteResponse
    {
        $response = $this->client->execute(
            PaymentMethodsEndpoints::delete(),
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodDeleteResponse) {
            throw new HyperswitchApiException('Unexpected response type from PaymentMethods::delete.');
        }
        return $response;
    }

    public function tokenDetails(string $temporaryToken): PaymentMethodTokenDetailsResponse
    {
        $response = $this->client->execute(
            PaymentMethodsEndpoints::tokenDetails(),
            pathParams: ['token' => $temporaryToken],
        );
        if (!$response instanceof PaymentMethodTokenDetailsResponse) {
            throw new HyperswitchApiException('Unexpected response type from PaymentMethods::tokenDetails.');
        }
        return $response;
    }
}
