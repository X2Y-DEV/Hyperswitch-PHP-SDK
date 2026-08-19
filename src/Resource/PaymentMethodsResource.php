<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Request\CreatePaymentMethodRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Request\UpdatePaymentMethodRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodDeleteResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodTokenDetailsResponse;
use X2Y\HyperSwitchClient\Endpoint\PaymentMethodsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch V1 Payment Methods group.
 *
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodsResource extends AbstractResource
{
    public function create(CreatePaymentMethodRequest $request): PaymentMethodResponse
    {
        $response = $this->execute(
            PaymentMethodsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethods::create.');
        }
        return $response;
    }

    public function retrieve(string $paymentMethodId): PaymentMethodResponse
    {
        $response = $this->execute(
            PaymentMethodsEndpoints::retrieve(),
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethods::retrieve.');
        }
        return $response;
    }

    public function update(string $paymentMethodId, UpdatePaymentMethodRequest $request): PaymentMethodResponse
    {
        $response = $this->execute(
            PaymentMethodsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethods::update.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<PaymentMethodResponse>
     */
    public function listForCustomer(string $customerId): PaginatedList
    {
        $response = $this->execute(
            PaymentMethodsEndpoints::listForCustomer(),
            pathParams: ['id' => $customerId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethods::listForCustomer.');
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
        $response = $this->execute(
            PaymentMethodsEndpoints::delete(),
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodDeleteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethods::delete.');
        }
        return $response;
    }

    public function tokenDetails(string $temporaryToken): PaymentMethodTokenDetailsResponse
    {
        $response = $this->execute(
            PaymentMethodsEndpoints::tokenDetails(),
            pathParams: ['token' => $temporaryToken],
        );
        if (!$response instanceof PaymentMethodTokenDetailsResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethods::tokenDetails.');
        }
        return $response;
    }
}
