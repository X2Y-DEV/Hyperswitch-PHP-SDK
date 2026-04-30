<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Mandate\Response\MandateResponse;
use X2Y\HyperswitchClient\Endpoint\MandatesEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;

/**
 * Typed facade over the Hyperswitch Mandates group.
 *
 * Mandate *creation* happens via `Payments::create()` with the appropriate
 * `mandate_data` / `customer_acceptance` / `setup_future_usage` fields — see
 * MandatesEndpoints for the polymorphism note.
 *
 * @postman             Mandates
 * @hyperswitch-source  2026-04-30
 */
final class MandatesResource implements ResourceInterface
{
    public function __construct(private readonly ApiClientInterface $client)
    {
    }

    public function retrieve(string $mandateId): MandateResponse
    {
        $response = $this->client->execute(
            MandatesEndpoints::retrieve(),
            pathParams: ['id' => $mandateId],
        );
        if (!$response instanceof MandateResponse) {
            throw new HyperswitchApiException('Unexpected response type from Mandates::retrieve.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<MandateResponse>
     */
    public function listForCustomer(string $customerId): PaginatedList
    {
        $response = $this->client->execute(
            MandatesEndpoints::listForCustomer(),
            pathParams: ['customer_id' => $customerId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from Mandates::listForCustomer.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): MandateResponse => MandateResponse::fromArray($row),
        );
    }

    public function revoke(string $mandateId): MandateResponse
    {
        $response = $this->client->execute(
            MandatesEndpoints::revoke(),
            pathParams: ['id' => $mandateId],
        );
        if (!$response instanceof MandateResponse) {
            throw new HyperswitchApiException('Unexpected response type from Mandates::revoke.');
        }
        return $response;
    }
}
