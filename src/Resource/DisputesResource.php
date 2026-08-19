<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\AttachEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\DeleteEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\SubmitEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\CreateFileResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputeListFiltersResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputeResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputesAggregateResponse;
use X2Y\HyperSwitchClient\Endpoint\DisputesEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Typed facade over the HyperSwitch Disputes group.
 *
 * @postman             Disputes
 * @hyperswitch-source  2026-04-30
 */
final class DisputesResource extends AbstractResource
{
    public function retrieve(string $disputeId): DisputeResponse
    {
        $response = $this->execute(
            DisputesEndpoints::retrieve(),
            pathParams: ['id' => $disputeId],
        );
        if (!$response instanceof DisputeResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::retrieve.');
        }
        return $response;
    }

    /**
     * @return PaginatedList<DisputeResponse>
     */
    public function list(): PaginatedList
    {
        $response = $this->execute(DisputesEndpoints::list());
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): DisputeResponse => DisputeResponse::fromArray($row),
        );
    }

    public function accept(string $disputeId): DisputeResponse
    {
        $response = $this->execute(
            DisputesEndpoints::accept(),
            pathParams: ['id' => $disputeId],
        );
        if (!$response instanceof DisputeResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::accept.');
        }
        return $response;
    }

    public function submitEvidence(SubmitEvidenceRequest $request): DisputeResponse
    {
        $response = $this->execute(
            DisputesEndpoints::submitEvidence(),
            request: $request,
        );
        if (!$response instanceof DisputeResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::submitEvidence.');
        }
        return $response;
    }

    /**
     * @openapi   "Attach Evidence to Dispute"
     */
    public function attachEvidence(AttachEvidenceRequest $request): CreateFileResponse
    {
        $response = $this->execute(
            DisputesEndpoints::attachEvidence(),
            request: $request,
        );
        if (!$response instanceof CreateFileResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::attachEvidence.');
        }
        return $response;
    }

    /**
     * @openapi   "Delete Evidence attached to a Dispute"
     */
    public function deleteEvidence(DeleteEvidenceRequest $request): void
    {
        $this->execute(
            DisputesEndpoints::deleteEvidence(),
            request: $request,
        );
    }

    /**
     * @openapi   "Retrieve a Dispute Evidence"
     *
     * @return list<array<string, mixed>> Raw evidence blocks for the dispute.
     */
    public function retrieveEvidence(string $disputeId): array
    {
        $response = $this->execute(
            DisputesEndpoints::retrieveEvidence(),
            pathParams: ['id' => $disputeId],
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::retrieveEvidence.');
        }
        $blocks = [];
        foreach (($response ?? []) as $row) {
            if (is_array($row)) {
                /** @var array<string, mixed> $row */
                $blocks[] = $row;
            }
        }
        return $blocks;
    }

    /**
     * @openapi   "Get Disputes Aggregate"
     */
    public function aggregate(\DateTimeImmutable $startTime): DisputesAggregateResponse
    {
        $response = $this->execute(
            DisputesEndpoints::aggregate(),
            queryParams: ['start_time' => $startTime->format(\DateTimeInterface::ATOM)],
        );
        if (!$response instanceof DisputesAggregateResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::aggregate.');
        }
        return $response;
    }

    /**
     * @openapi   "List all filters for disputes"
     */
    public function filters(): DisputeListFiltersResponse
    {
        $response = $this->execute(DisputesEndpoints::filters());
        if (!$response instanceof DisputeListFiltersResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::filters.');
        }
        return $response;
    }

    /**
     * @openapi   "Get Disputes Aggregate for Profiles"
     */
    public function profileAggregate(\DateTimeImmutable $startTime): DisputesAggregateResponse
    {
        $response = $this->execute(
            DisputesEndpoints::profileAggregate(),
            queryParams: ['start_time' => $startTime->format(\DateTimeInterface::ATOM)],
        );
        if (!$response instanceof DisputesAggregateResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::profileAggregate.');
        }
        return $response;
    }

    /**
     * @openapi   "List all filters for disputes for the given Profiles"
     */
    public function profileFilters(): DisputeListFiltersResponse
    {
        $response = $this->execute(DisputesEndpoints::profileFilters());
        if (!$response instanceof DisputeListFiltersResponse) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::profileFilters.');
        }
        return $response;
    }

    /**
     * @openapi   "List Disputes for The given Profiles"
     *
     * @param array<string, scalar|array<string, scalar>> $queryParams
     * @return PaginatedList<DisputeResponse>
     */
    public function profileList(array $queryParams = []): PaginatedList
    {
        $response = $this->execute(
            DisputesEndpoints::profileList(),
            queryParams: $queryParams,
        );
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from Disputes::profileList.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return PaginatedList::fromArray(
            $raw,
            static fn (array $row): DisputeResponse => DisputeResponse::fromArray($row),
        );
    }
}
