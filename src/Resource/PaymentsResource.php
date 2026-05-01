<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CancelPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CancelPostCapturePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CapturePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ConfirmPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\IncrementalAuthorizationRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ListPaymentsRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\SessionTokensRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\UpdatePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\SessionTokensResponse;
use X2Y\HyperSwitchClient\Endpoint\PaymentsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Payments API group.
 *
 * @postman             Payments
 * @hyperswitch-source  2026-04-30
 */
final class PaymentsResource extends AbstractResource
{
    /**
     * @postman   Payments / Payments - Create
     */
    public function create(CreatePaymentRequest $request): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::create.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Update
     */
    public function update(string $paymentId, UpdatePaymentRequest $request): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::update.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Retrieve
     *
     * @param array<string, scalar|array<string, scalar>> $queryParams
     *        Optional query flags such as ['force_sync' => 'true', 'expand_attempts' => 'true'].
     */
    public function retrieve(string $paymentId, array $queryParams = []): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::retrieve(),
            pathParams:  ['id' => $paymentId],
            queryParams: $queryParams,
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::retrieve.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Confirm
     * @postman   Payments / Payments - Confirm with token
     *
     * Both rich and token-based confirms hit the same endpoint — populate
     * `ConfirmPaymentRequest` differently per flow.
     */
    public function confirm(string $paymentId, ConfirmPaymentRequest $request): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::confirm(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::confirm.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Capture
     */
    public function capture(string $paymentId, ?CapturePaymentRequest $request = null): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::capture(),
            request:    $request ?? new CapturePaymentRequest(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::capture.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Cancel
     */
    public function cancel(string $paymentId, ?CancelPaymentRequest $request = null): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::cancel(),
            request:    $request ?? new CancelPaymentRequest(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::cancel.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Cancel Post Capture
     */
    public function cancelPostCapture(
        string $paymentId,
        ?CancelPostCapturePaymentRequest $request = null,
    ): PaymentResponse {
        $response = $this->execute(
            PaymentsEndpoints::cancelPostCapture(),
            request:    $request ?? new CancelPostCapturePaymentRequest(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::cancelPostCapture.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payment-List
     *
     * @return PaginatedList<PaymentResponse>
     */
    public function list(?ListPaymentsRequest $request = null): PaginatedList
    {
        $queryParams = $request?->toArray() ?? [];

        $response = $this->execute(
            PaymentsEndpoints::list(),
            queryParams: $queryParams,
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): PaymentResponse => PaymentResponse::fromArray($row),
        );
    }

    /**
     * @postman   Payments / Session Token
     */
    public function sessionTokens(SessionTokensRequest $request): SessionTokensResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::sessionTokens(),
            request: $request,
        );
        if (!$response instanceof SessionTokensResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::sessionTokens.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payments - Incremental Authorization
     */
    public function incrementalAuthorization(
        string $paymentId,
        IncrementalAuthorizationRequest $request,
    ): PaymentResponse {
        $response = $this->execute(
            PaymentsEndpoints::incrementalAuthorization(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::incrementalAuthorization.');
        }
        return $response;
    }

    /**
     * @postman   Payments / Payment - Extend Authorization
     */
    public function extendAuthorization(string $paymentId): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::extendAuthorization(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::extendAuthorization.');
        }
        return $response;
    }
}
