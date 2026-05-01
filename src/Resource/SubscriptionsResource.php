<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Subscription\Request\CancelSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\ConfirmSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\CreateAndConfirmSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\CreateSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\EstimateSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\ListPlansRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\ListSubscriptionsRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\PauseSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\ResumeSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Request\UpdateSubscriptionRequest;
use X2Y\HyperswitchClient\Dto\Subscription\Response\EstimateResponse;
use X2Y\HyperswitchClient\Dto\Subscription\Response\PlanResponse;
use X2Y\HyperswitchClient\Dto\Subscription\Response\SubscriptionResponse;
use X2Y\HyperswitchClient\Endpoint\SubscriptionsEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * Typed facade over the Hyperswitch Subscriptions API group.
 *
 * @postman             Subscriptions
 * @hyperswitch-source  2026-04-30
 */
final class SubscriptionsResource extends AbstractResource
{
    /**
     * @postman   Subscriptions / Subscription - Create
     */
    public function create(CreateSubscriptionRequest $request): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::create(),
            request: $request,
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::create.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Create + Confirm
     */
    public function createAndConfirm(CreateAndConfirmSubscriptionRequest $request): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::createAndConfirm(),
            request: $request,
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::createAndConfirm.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Confirm
     */
    public function confirm(string $subscriptionId, ConfirmSubscriptionRequest $request): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::confirm(),
            request:    $request,
            pathParams: ['id' => $subscriptionId],
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::confirm.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Update
     */
    public function update(string $subscriptionId, UpdateSubscriptionRequest $request): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $subscriptionId],
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::update.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Retrieve
     */
    public function retrieve(string $subscriptionId): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::retrieve(),
            pathParams: ['id' => $subscriptionId],
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::retrieve.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Pause
     */
    public function pause(string $subscriptionId, ?PauseSubscriptionRequest $request = null): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::pause(),
            request:    $request ?? new PauseSubscriptionRequest(),
            pathParams: ['id' => $subscriptionId],
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::pause.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Resume
     */
    public function resume(string $subscriptionId, ?ResumeSubscriptionRequest $request = null): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::resume(),
            request:    $request ?? new ResumeSubscriptionRequest(),
            pathParams: ['id' => $subscriptionId],
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::resume.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - Cancel
     */
    public function cancel(string $subscriptionId, ?CancelSubscriptionRequest $request = null): SubscriptionResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::cancel(),
            request:    $request ?? new CancelSubscriptionRequest(),
            pathParams: ['id' => $subscriptionId],
        );
        if (!$response instanceof SubscriptionResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::cancel.');
        }
        return $response;
    }

    /**
     * @postman   Subscriptions / Subscription - List
     *
     * @return PaginatedList<SubscriptionResponse>
     */
    public function list(?ListSubscriptionsRequest $request = null): PaginatedList
    {
        $queryParams = $request?->toArray() ?? [];

        $response = $this->execute(
            SubscriptionsEndpoints::list(),
            queryParams: $queryParams,
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): SubscriptionResponse => SubscriptionResponse::fromArray($row),
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Plans
     *
     * @return PaginatedList<PlanResponse>
     */
    public function plans(?ListPlansRequest $request = null): PaginatedList
    {
        $queryParams = $request?->toArray() ?? [];

        $response = $this->execute(
            SubscriptionsEndpoints::plans(),
            queryParams: $queryParams,
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::plans.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): PlanResponse => PlanResponse::fromArray($row),
        );
    }

    /**
     * @postman   Subscriptions / Subscription - Estimate
     */
    public function estimate(EstimateSubscriptionRequest $request): EstimateResponse
    {
        $response = $this->execute(
            SubscriptionsEndpoints::estimate(),
            queryParams: $request->toArray(),
        );
        if (!$response instanceof EstimateResponse) {
            throw new HyperswitchApiException('Unexpected response type from Subscriptions::estimate.');
        }
        return $response;
    }
}
