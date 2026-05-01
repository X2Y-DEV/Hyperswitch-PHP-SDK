<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Common\PaginatedList;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\SubmitEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputeResponse;
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
}
