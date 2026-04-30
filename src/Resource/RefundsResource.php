<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Refund\Request\CreateRefundRequest;
use X2Y\HyperswitchClient\Dto\Refund\Request\ListRefundsRequest;
use X2Y\HyperswitchClient\Dto\Refund\Request\UpdateRefundRequest;
use X2Y\HyperswitchClient\Dto\Refund\Response\RefundResponse;
use X2Y\HyperswitchClient\Endpoint\RefundsEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;

/**
 * Typed facade over the Hyperswitch Refunds API group.
 *
 * @postman             Refunds
 * @hyperswitch-source  2026-04-30
 */
final class RefundsResource implements ResourceInterface
{
    public function __construct(private readonly ApiClientInterface $client)
    {
    }

    /**
     * @postman   Refunds / Refunds - Create
     */
    public function create(CreateRefundRequest $request): RefundResponse
    {
        $response = $this->client->execute(
            RefundsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof RefundResponse) {
            throw new HyperswitchApiException('Unexpected response type from Refunds::create.');
        }
        return $response;
    }

    /**
     * @postman   Refunds / Refunds - Update
     */
    public function update(string $refundId, UpdateRefundRequest $request): RefundResponse
    {
        $response = $this->client->execute(
            RefundsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $refundId],
        );
        if (!$response instanceof RefundResponse) {
            throw new HyperswitchApiException('Unexpected response type from Refunds::update.');
        }
        return $response;
    }

    /**
     * @postman   Refunds / Refunds - Retrieve
     */
    public function retrieve(string $refundId): RefundResponse
    {
        $response = $this->client->execute(
            RefundsEndpoints::retrieve(),
            pathParams: ['id' => $refundId],
        );
        if (!$response instanceof RefundResponse) {
            throw new HyperswitchApiException('Unexpected response type from Refunds::retrieve.');
        }
        return $response;
    }

    /**
     * @postman   Refunds / Refunds - List
     *
     * Returns the raw decoded list envelope (Hyperswitch wraps results in
     * `{"data": [...], "size": N}` or similar). A dedicated PaginatedList DTO
     * will replace this once cross-resource list shapes are unified.
     *
     * @return array<string, mixed>
     */
    public function list(ListRefundsRequest $request): array
    {
        $response = $this->client->execute(
            RefundsEndpoints::list(),
            request: $request,
        );
        if ($response === null) {
            return [];
        }
        if (!is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from Refunds::list.');
        }
        return $response;
    }
}
