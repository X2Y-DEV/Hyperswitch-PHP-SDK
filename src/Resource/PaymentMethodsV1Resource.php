<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodDeleteResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request\CreatePaymentMethodV1Request;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request\UpdatePaymentMethodV1Request;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Response\CustomerDefaultPaymentMethodResponse;
use X2Y\HyperSwitchClient\Endpoint\PaymentMethodsV1Endpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch v1 payment-methods surface. See
 * PaymentMethodsResource for the v2-compat modular flow (`/v1/payment-methods`).
 *
 * The four list endpoints return heterogeneous, connector-shaped payloads; the
 * Resource returns them as the raw decoded array per the connector-blob convention.
 *
 * @openapi             tag: Payment Methods
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PaymentMethodsV1Resource extends AbstractResource
{
    /**
     * @openapi   "Create a Payment Method"
     */
    public function create(CreatePaymentMethodV1Request $request): PaymentMethodResponse
    {
        $response = $this->execute(PaymentMethodsV1Endpoints::create(), request: $request);
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodsV1::create.');
        }
        return $response;
    }

    /**
     * @openapi   "Retrieve a Payment method"
     */
    public function retrieve(string $paymentMethodId): PaymentMethodResponse
    {
        $response = $this->execute(
            PaymentMethodsV1Endpoints::retrieve(),
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodsV1::retrieve.');
        }
        return $response;
    }

    /**
     * @openapi   "Update a Payment method"
     */
    public function update(string $paymentMethodId, UpdatePaymentMethodV1Request $request): PaymentMethodResponse
    {
        $response = $this->execute(
            PaymentMethodsV1Endpoints::update(),
            request:    $request,
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodsV1::update.');
        }
        return $response;
    }

    /**
     * @openapi   "Delete a Payment method"
     */
    public function delete(string $paymentMethodId): PaymentMethodDeleteResponse
    {
        $response = $this->execute(
            PaymentMethodsV1Endpoints::delete(),
            pathParams: ['id' => $paymentMethodId],
        );
        if (!$response instanceof PaymentMethodDeleteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodsV1::delete.');
        }
        return $response;
    }

    /**
     * @openapi   "List all Payment Methods for a Merchant"
     *
     * @return array<int|string, mixed>
     */
    public function listForMerchant(): array
    {
        return $this->rawList(PaymentMethodsV1Endpoints::listForMerchant(), [], 'listForMerchant');
    }

    /**
     * @openapi   "List Customer Payment Methods via Client Secret"
     *
     * @param array<string, scalar> $queryParams e.g. ['client_secret' => 'pay_..._secret_...'].
     * @return array<int|string, mixed>
     */
    public function listForCustomerViaClientSecret(array $queryParams = []): array
    {
        return $this->rawList(
            PaymentMethodsV1Endpoints::listForCustomerViaClientSecret(),
            $queryParams,
            'listForCustomerViaClientSecret',
        );
    }

    /**
     * @openapi   "List all Payment Methods for a Customer"
     *
     * @return array<int|string, mixed>
     */
    public function listForCustomer(string $customerId): array
    {
        return $this->rawList(
            PaymentMethodsV1Endpoints::listForCustomer(),
            [],
            'listForCustomer',
            ['id' => $customerId],
        );
    }

    /**
     * @openapi   "List Payment Methods for a Payment via Client SDK"
     *
     * @return array<int|string, mixed>
     */
    public function listForPaymentClient(string $paymentId): array
    {
        return $this->rawList(
            PaymentMethodsV1Endpoints::listForPaymentClient(),
            [],
            'listForPaymentClient',
            ['id' => $paymentId],
        );
    }

    /**
     * @openapi   "Set the Payment Method as Default"
     */
    public function setDefault(string $customerId, string $paymentMethodId): CustomerDefaultPaymentMethodResponse
    {
        $response = $this->execute(
            PaymentMethodsV1Endpoints::setDefault(),
            pathParams: ['customer_id' => $customerId, 'payment_method_id' => $paymentMethodId],
        );
        if (!$response instanceof CustomerDefaultPaymentMethodResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodsV1::setDefault.');
        }
        return $response;
    }

    /**
     * @param array<string, scalar>       $queryParams
     * @param array<string, string>       $pathParams
     * @return array<int|string, mixed>
     */
    private function rawList(
        \X2Y\HyperSwitchClient\Transport\Endpoint $endpoint,
        array $queryParams,
        string $context,
        array $pathParams = [],
    ): array {
        $response = $this->execute($endpoint, pathParams: $pathParams, queryParams: $queryParams);
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException("Unexpected response type from PaymentMethodsV1::{$context}.");
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return $raw;
    }
}
