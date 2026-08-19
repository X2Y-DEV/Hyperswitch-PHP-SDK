<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\Refund\Request\CreateRefundRequest;
use X2Y\HyperSwitchClient\Dto\Refund\Request\ListRefundsRequest;
use X2Y\HyperSwitchClient\Dto\Refund\Request\UpdateRefundRequest;
use X2Y\HyperSwitchClient\Dto\Refund\Response\RefundResponse;
use X2Y\HyperSwitchClient\Endpoint\RefundsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Refunds API group.
 *
 * @hyperswitch-source  2026-04-30
 */
final class RefundsResource extends AbstractResource
{
    public function create(CreateRefundRequest $request): RefundResponse
    {
        $response = $this->execute(
            RefundsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof RefundResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Refunds::create.');
        }
        return $response;
    }

    public function update(string $refundId, UpdateRefundRequest $request): RefundResponse
    {
        $response = $this->execute(
            RefundsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $refundId],
        );
        if (!$response instanceof RefundResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Refunds::update.');
        }
        return $response;
    }

    public function retrieve(string $refundId): RefundResponse
    {
        $response = $this->execute(
            RefundsEndpoints::retrieve(),
            pathParams: ['id' => $refundId],
        );
        if (!$response instanceof RefundResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Refunds::retrieve.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<RefundResponse>
     */
    public function list(ListRefundsRequest $request): PaginatedList
    {
        $response = $this->execute(
            RefundsEndpoints::list(),
            request: $request,
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from Refunds::list.');
        }
        /** @var array<string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): RefundResponse => RefundResponse::fromArray($row),
        );
    }
}
