<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Connector\Request\CreateConnectorRequest;
use X2Y\HyperswitchClient\Dto\Connector\Request\UpdateConnectorRequest;
use X2Y\HyperswitchClient\Dto\Connector\Response\ConnectorDeleteResponse;
use X2Y\HyperswitchClient\Dto\Connector\Response\ConnectorResponse;
use X2Y\HyperswitchClient\Endpoint\ConnectorsEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * Typed facade over the Hyperswitch Payment Connectors group. Admin-tier.
 *
 * @postman             PaymentConnectors
 * @hyperswitch-source  2026-04-30
 */
final class ConnectorsResource extends AbstractResource
{
    public function create(string $accountId, CreateConnectorRequest $request): ConnectorResponse
    {
        $response = $this->execute(
            ConnectorsEndpoints::create(),
            request:    $request,
            pathParams: ['account_id' => $accountId],
        );
        if (!$response instanceof ConnectorResponse) {
            throw new HyperswitchApiException('Unexpected response type from Connectors::create.');
        }
        return $response;
    }

    public function retrieve(string $accountId, string $connectorId): ConnectorResponse
    {
        $response = $this->execute(
            ConnectorsEndpoints::retrieve(),
            pathParams: ['account_id' => $accountId, 'connector_id' => $connectorId],
        );
        if (!$response instanceof ConnectorResponse) {
            throw new HyperswitchApiException('Unexpected response type from Connectors::retrieve.');
        }
        return $response;
    }

    public function update(string $accountId, string $connectorId, UpdateConnectorRequest $request): ConnectorResponse
    {
        $response = $this->execute(
            ConnectorsEndpoints::update(),
            request:    $request,
            pathParams: ['account_id' => $accountId, 'connector_id' => $connectorId],
        );
        if (!$response instanceof ConnectorResponse) {
            throw new HyperswitchApiException('Unexpected response type from Connectors::update.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<ConnectorResponse>
     */
    public function list(string $accountId): PaginatedList
    {
        $response = $this->execute(
            ConnectorsEndpoints::list(),
            pathParams: ['account_id' => $accountId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from Connectors::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): ConnectorResponse => ConnectorResponse::fromArray($row),
        );
    }

    public function delete(string $accountId, string $connectorId): ConnectorDeleteResponse
    {
        $response = $this->execute(
            ConnectorsEndpoints::delete(),
            pathParams: ['account_id' => $accountId, 'connector_id' => $connectorId],
        );
        if (!$response instanceof ConnectorDeleteResponse) {
            throw new HyperswitchApiException('Unexpected response type from Connectors::delete.');
        }
        return $response;
    }
}
