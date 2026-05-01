<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Payout\Request\CancelPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\ConfirmPayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\CreatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\UpdatePayoutRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutResponse;
use X2Y\HyperSwitchClient\Endpoint\PayoutsEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Payouts group. Payout-link flows reuse
 * the same endpoints — see PayoutsEndpoints for the polymorphism note.
 *
 * @postman             Payouts
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
}
