<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\CardIssuer\Request\CardIssuerRequest;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Request\CardIssuerUpdateRequest;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Response\CardIssuerDeleteResponse;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Response\CardIssuerResponse;
use X2Y\HyperSwitchClient\Endpoint\CardIssuerEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * @openapi             tag: Card Issuer
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CardIssuerResource extends AbstractResource
{
    /** @openapi "Create Card Issuer" */
    public function create(CardIssuerRequest $request): CardIssuerResponse
    {
        return $this->asIssuer($this->execute(CardIssuerEndpoints::create(), request: $request), 'create');
    }

    /** @openapi "Update Card Issuer" */
    public function update(string $cardIssuerId, CardIssuerUpdateRequest $request): CardIssuerResponse
    {
        $response = $this->execute(
            CardIssuerEndpoints::update(),
            request:    $request,
            pathParams: ['id' => $cardIssuerId],
        );
        return $this->asIssuer($response, 'update');
    }

    /** @openapi "Delete Card Issuer" */
    public function delete(string $cardIssuerId): CardIssuerDeleteResponse
    {
        $response = $this->execute(CardIssuerEndpoints::delete(), pathParams: ['id' => $cardIssuerId]);
        if (!$response instanceof CardIssuerDeleteResponse) {
            throw new HyperSwitchApiException('Unexpected response type from CardIssuer::delete.');
        }
        return $response;
    }

    /**
     * @openapi "List Card Issuers"
     *
     * @return array<int|string, mixed>
     */
    public function list(): array
    {
        $response = $this->execute(CardIssuerEndpoints::list());
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException('Unexpected response type from CardIssuer::list.');
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return $raw;
    }

    private function asIssuer(mixed $response, string $context): CardIssuerResponse
    {
        if (!$response instanceof CardIssuerResponse) {
            throw new HyperSwitchApiException("Unexpected response type from CardIssuer::{$context}.");
        }
        return $response;
    }
}
