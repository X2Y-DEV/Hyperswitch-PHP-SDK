<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Event\Request\EventListConstraintsRequest;
use X2Y\HyperSwitchClient\Dto\Event\Response\EventRetrieveResponse;
use X2Y\HyperSwitchClient\Dto\Event\Response\TotalEventsResponse;
use X2Y\HyperSwitchClient\Endpoint\EventsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Events group (webhook delivery). JWT auth.
 *
 * @openapi             tag: Event
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class EventsResource extends AbstractResource
{
    /** @openapi "List all Events associated with a Merchant Account or Profile" */
    public function listForMerchant(string $merchantId, EventListConstraintsRequest $request): TotalEventsResponse
    {
        $response = $this->execute(
            EventsEndpoints::listForMerchant(),
            request:    $request,
            pathParams: ['merchant_id' => $merchantId],
        );
        return $this->asTotal($response, 'listForMerchant');
    }

    /** @openapi "List all Events associated with a Profile" */
    public function listForProfile(EventListConstraintsRequest $request): TotalEventsResponse
    {
        $response = $this->execute(EventsEndpoints::listForProfile(), request: $request);
        return $this->asTotal($response, 'listForProfile');
    }

    /**
     * @openapi "List all delivery attempts for an Event"
     *
     * @return list<array<string, mixed>>
     */
    public function attempts(string $merchantId, string $eventId): array
    {
        $response = $this->execute(
            EventsEndpoints::attempts(),
            pathParams: ['merchant_id' => $merchantId, 'event_id' => $eventId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from Events::attempts.');
        }
        $out = [];
        foreach (($response ?? []) as $row) {
            if (is_array($row)) {
                /** @var array<string, mixed> $row */
                $out[] = $row;
            }
        }
        return $out;
    }

    /** @openapi "Manually retry the delivery of an Event" */
    public function retry(string $merchantId, string $eventId): EventRetrieveResponse
    {
        $response = $this->execute(
            EventsEndpoints::retry(),
            pathParams: ['merchant_id' => $merchantId, 'event_id' => $eventId],
        );
        if (!$response instanceof EventRetrieveResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Events::retry.');
        }
        return $response;
    }

    private function asTotal(mixed $response, string $context): TotalEventsResponse
    {
        if (!$response instanceof TotalEventsResponse) {
            throw new HyperSwitchApiException("Unexpected response type from Events::{$context}.");
        }
        return $response;
    }
}
