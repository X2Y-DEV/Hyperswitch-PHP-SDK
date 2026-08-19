<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CancelPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CancelPostCapturePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CapturePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CompleteAuthorizePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ConfirmPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\EligibilityCheckRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\EligibilityRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ExternalAuthenticationRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\IncrementalAuthorizationRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ListPaymentsRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\PostSessionTokensRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\SessionTokensRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\UpdateMetadataPaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\UpdatePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Response\EligibilityCheckResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\EligibilityResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\ExternalAuthenticationResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\PostSessionTokensResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\SessionTokensResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\UpdateMetadataPaymentResponse;
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

    /**
     * @openapi   "Cancel a Payment Post Capture Retrieve"
     */
    public function cancelPostCaptureRetrieve(string $paymentId): PaymentResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::cancelPostCaptureRetrieve(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::cancelPostCaptureRetrieve.');
        }
        return $response;
    }

    /**
     * @openapi   "Complete Authorize a Payment"
     */
    public function completeAuthorize(
        string $paymentId,
        ?CompleteAuthorizePaymentRequest $request = null,
    ): PaymentResponse {
        $response = $this->execute(
            PaymentsEndpoints::completeAuthorize(),
            request:    $request ?? new CompleteAuthorizePaymentRequest(),
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::completeAuthorize.');
        }
        return $response;
    }

    /**
     * @openapi   "Initiate external authentication for a Payment"
     */
    public function externalAuthentication(
        string $paymentId,
        ExternalAuthenticationRequest $request,
    ): ExternalAuthenticationResponse {
        $response = $this->execute(
            PaymentsEndpoints::externalAuthentication(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof ExternalAuthenticationResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::externalAuthentication.');
        }
        return $response;
    }

    /**
     * @openapi   "Submit Eligibility data for a Payment"
     */
    public function eligibility(string $paymentId, EligibilityRequest $request): EligibilityResponse
    {
        $response = $this->execute(
            PaymentsEndpoints::eligibility(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof EligibilityResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::eligibility.');
        }
        return $response;
    }

    /**
     * @openapi   "Submit Eligibility Check data for a Payment"
     */
    public function eligibilityCheck(
        string $paymentId,
        EligibilityCheckRequest $request,
    ): EligibilityCheckResponse {
        $response = $this->execute(
            PaymentsEndpoints::eligibilityCheck(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof EligibilityCheckResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::eligibilityCheck.');
        }
        return $response;
    }

    /**
     * @openapi   "Create Post Session Tokens for a Payment"
     */
    public function postSessionTokens(
        string $paymentId,
        PostSessionTokensRequest $request,
    ): PostSessionTokensResponse {
        $response = $this->execute(
            PaymentsEndpoints::postSessionTokens(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof PostSessionTokensResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::postSessionTokens.');
        }
        return $response;
    }

    /**
     * @openapi   "Update Metadata for a Payment"
     */
    public function updateMetadata(
        string $paymentId,
        UpdateMetadataPaymentRequest $request,
    ): UpdateMetadataPaymentResponse {
        $response = $this->execute(
            PaymentsEndpoints::updateMetadata(),
            request:    $request,
            pathParams: ['id' => $paymentId],
        );
        if (!$response instanceof UpdateMetadataPaymentResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payments::updateMetadata.');
        }
        return $response;
    }
}
