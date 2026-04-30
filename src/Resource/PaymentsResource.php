<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Endpoint\PaymentsEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;

/**
 * Typed facade over the Hyperswitch Payments API group.
 *
 * Stub: only `create` and `retrieve` implemented. AI maintenance adds the rest
 * (update, confirm, capture, cancel, cancelPostCapture, list, sessionTokens,
 * incrementalAuthorization, extendAuthorization).
 *
 * @postman             Payments
 * @hyperswitch-source  2026-04-30
 */
final class PaymentsResource implements ResourceInterface
{
    public function __construct(private readonly ApiClientInterface $client)
    {
    }

    /**
     * @postman   Payments / Payments - Create
     */
    public function create(CreatePaymentRequest $request): PaymentResponse
    {
        $response = $this->client->execute(
            PaymentsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperswitchApiException('Unexpected response type from Payments::create.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Retrieve
     */
    public function retrieve(string $paymentId): PaymentResponse
    {
        $response = $this->client->execute(
            PaymentsEndpoints::retrieve(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperswitchApiException('Unexpected response type from Payments::retrieve.');
        }
        return $response;
    }
}
