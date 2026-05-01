<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\ConfirmSessionRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\CreateSessionRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\SessionTokenLookupRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Request\UpdateSavedPaymentMethodRequest;
use X2Y\HyperSwitchClient\Dto\PaymentMethodSession\Response\SessionResponse;
use X2Y\HyperSwitchClient\Endpoint\PaymentMethodSessionsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch V1 Payment Method Sessions group.
 *
 * @postman             Payment Method Sessions
 * @hyperswitch-source  2026-04-30
 */
final class PaymentMethodSessionsResource extends AbstractResource
{
    public function create(CreateSessionRequest $request): SessionResponse
    {
        $response = $this->execute(
            PaymentMethodSessionsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof SessionResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodSessions::create.');
        }
        return $response;
    }

    public function retrieve(string $sessionId): SessionResponse
    {
        $response = $this->execute(
            PaymentMethodSessionsEndpoints::retrieve(),
            pathParams: ['id' => $sessionId],
        );
        if (!$response instanceof SessionResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodSessions::retrieve.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<PaymentMethodResponse>
     */
    public function listPaymentMethods(string $sessionId): PaginatedList
    {
        $response = $this->execute(
            PaymentMethodSessionsEndpoints::listPaymentMethods(),
            pathParams: ['id' => $sessionId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException(
                'Unexpected response type from PaymentMethodSessions::listPaymentMethods.'
            );
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): PaymentMethodResponse => PaymentMethodResponse::fromArray($row),
        );
    }

    public function updateSavedPaymentMethod(
        string $sessionId,
        UpdateSavedPaymentMethodRequest $request,
    ): SessionResponse {
        $response = $this->execute(
            PaymentMethodSessionsEndpoints::updateSavedPaymentMethod(),
            request:    $request,
            pathParams: ['id' => $sessionId],
        );
        if (!$response instanceof SessionResponse) {
            throw new HyperSwitchApiException(
                'Unexpected response type from PaymentMethodSessions::updateSavedPaymentMethod.'
            );
        }
        return $response;
    }

    public function confirm(string $sessionId, ConfirmSessionRequest $request): SessionResponse
    {
        $response = $this->execute(
            PaymentMethodSessionsEndpoints::confirm(),
            request:    $request,
            pathParams: ['id' => $sessionId],
        );
        if (!$response instanceof SessionResponse) {
            throw new HyperSwitchApiException('Unexpected response type from PaymentMethodSessions::confirm.');
        }
        return $response;
    }

    /**
     * Token-lookup / revocation. See `PaymentMethodSessionsEndpoints::tokenLookup`
     * for the unusual HTTP-method note.
     */
    public function tokenLookup(string $token, SessionTokenLookupRequest $request): SessionResponse
    {
        $response = $this->execute(
            PaymentMethodSessionsEndpoints::tokenLookup(),
            request:    $request,
            pathParams: ['token' => $token],
        );
        if (!$response instanceof SessionResponse) {
            throw new HyperSwitchApiException(
                'Unexpected response type from PaymentMethodSessions::tokenLookup.'
            );
        }
        return $response;
    }
}
