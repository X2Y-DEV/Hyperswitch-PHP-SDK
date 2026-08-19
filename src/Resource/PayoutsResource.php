<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CancelPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\ConfirmPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CreatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\FilterPayoutsRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\ListPayoutsRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\PayoutFiltersRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\UpdatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutListFiltersResponse;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutResponse;
use X2Y\HyperSwitchClient\Endpoint\PayoutsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Payouts group. Payout-link flows reuse
 * the same endpoints — see PayoutsEndpoints for the polymorphism note.
 *
 * @hyperswitch-source  2026-04-30
 */
final class PayoutsResource extends AbstractResource
{
    public function create(CreatePayoutRequest $request): PayoutResponse
    {
        $response = $this->execute(PayoutsEndpoints::create(), request: $request);
        if (!$response instanceof PayoutResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::create.');
        }
        return $response;
    }

    public function update(string $payoutId, UpdatePayoutRequest $request): PayoutResponse
    {
        $response = $this->execute(
            PayoutsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $payoutId],
        );
        if (!$response instanceof PayoutResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::update.');
        }
        return $response;
    }

    public function retrieve(string $payoutId): PayoutResponse
    {
        $response = $this->execute(
            PayoutsEndpoints::retrieve(),
            pathParams: ['id' => $payoutId],
        );
        if (!$response instanceof PayoutResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::retrieve.');
        }
        return $response;
    }

    public function confirm(string $payoutId, ?ConfirmPayoutRequest $request = null): PayoutResponse
    {
        $response = $this->execute(
            PayoutsEndpoints::confirm(),
            request:    $request ?? new ConfirmPayoutRequest(),
            pathParams: ['id' => $payoutId],
        );
        if (!$response instanceof PayoutResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::confirm.');
        }
        return $response;
    }

    public function fulfill(string $payoutId): PayoutResponse
    {
        $response = $this->execute(
            PayoutsEndpoints::fulfill(),
            pathParams: ['id' => $payoutId],
        );
        if (!$response instanceof PayoutResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::fulfill.');
        }
        return $response;
    }

    public function cancel(string $payoutId, ?CancelPayoutRequest $request = null): PayoutResponse
    {
        $response = $this->execute(
            PayoutsEndpoints::cancel(),
            request:    $request ?? new CancelPayoutRequest(),
            pathParams: ['id' => $payoutId],
        );
        if (!$response instanceof PayoutResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::cancel.');
        }
        return $response;
    }

    /**
     * @openapi   "List payouts using generic constraints"
     *
     * @return PaginatedList<PayoutResponse>
     */
    public function list(?ListPayoutsRequest $request = null): PaginatedList
    {
        $response = $this->execute(
            PayoutsEndpoints::list(),
            queryParams: $request?->toArray() ?? [],
        );
        return $this->toPaginatedList($response, 'Payouts::list');
    }

    /**
     * @openapi   "Filter payouts using specific constraints"
     *
     * @return PaginatedList<PayoutResponse>
     */
    public function filterList(FilterPayoutsRequest $request): PaginatedList
    {
        $response = $this->execute(
            PayoutsEndpoints::filterList(),
            request: $request,
        );
        return $this->toPaginatedList($response, 'Payouts::filterList');
    }

    /**
     * @openapi   "List available payout filters"
     */
    public function filters(PayoutFiltersRequest $request): PayoutListFiltersResponse
    {
        $response = $this->execute(
            PayoutsEndpoints::filters(),
            request: $request,
        );
        if (!$response instanceof PayoutListFiltersResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Payouts::filters.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<PayoutResponse>
     */
    private function toPaginatedList(mixed $response, string $context): PaginatedList
    {
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException("Unexpected response type from {$context}.");
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): PayoutResponse => PayoutResponse::fromArray($row),
        );
    }
}
