<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Routing\Request\DynamicRoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\RoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\RoutingEvaluateRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\UpdateDefaultRoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\UpdateScoreRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Response\RoutingDictionaryRecord;
use X2Y\HyperSwitchClient\Endpoint\RoutingEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * Typed facade over the HyperSwitch Routing group.
 *
 * @openapi             tag: Routing
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class RoutingResource extends AbstractResource
{
    /**
     * @openapi "List routing configs"
     *
     * @return array<int|string, mixed>
     */
    public function list(): array
    {
        return $this->raw(RoutingEndpoints::list(), 'list');
    }

    /**
     * @openapi "Retrieve a routing config"
     *
     * @return array<int|string, mixed>
     */
    public function retrieve(string $routingAlgorithmId): array
    {
        return $this->raw(RoutingEndpoints::retrieve(), 'retrieve', ['id' => $routingAlgorithmId]);
    }

    /**
     * @openapi "Retrieve active config"
     *
     * @return array<int|string, mixed>
     */
    public function active(): array
    {
        return $this->raw(RoutingEndpoints::active(), 'active');
    }

    /**
     * @openapi "Retrieve default fallback config"
     *
     * @return array<int|string, mixed>
     */
    public function defaultConfig(): array
    {
        return $this->raw(RoutingEndpoints::defaultConfig(), 'defaultConfig');
    }

    /**
     * @openapi "Retrieve default configs for all profiles"
     *
     * @return array<int|string, mixed>
     */
    public function defaultConfigForProfiles(): array
    {
        return $this->raw(RoutingEndpoints::defaultConfigForProfiles(), 'defaultConfigForProfiles');
    }

    /** @openapi "Create a routing config" */
    public function create(RoutingConfigRequest $request): RoutingDictionaryRecord
    {
        return $this->record(RoutingEndpoints::create(), 'create', request: $request);
    }

    /** @openapi "Activate a routing config" */
    public function activate(string $routingAlgorithmId): RoutingDictionaryRecord
    {
        return $this->record(RoutingEndpoints::activate(), 'activate', pathParams: ['id' => $routingAlgorithmId]);
    }

    /** @openapi "Deactivate a routing config" */
    public function deactivate(RoutingConfigRequest $request): RoutingDictionaryRecord
    {
        return $this->record(RoutingEndpoints::deactivate(), 'deactivate', request: $request);
    }

    /**
     * @openapi "Update default fallback config"
     *
     * @return array<int|string, mixed>
     */
    public function updateDefaultConfig(UpdateDefaultRoutingConfigRequest $request): array
    {
        return $this->raw(RoutingEndpoints::updateDefaultConfig(), 'updateDefaultConfig', request: $request);
    }

    /**
     * @openapi "Update default configs for all profiles"
     *
     * @return array<int|string, mixed>
     */
    public function updateDefaultConfigForProfile(string $profileId, UpdateDefaultRoutingConfigRequest $request): array
    {
        return $this->raw(
            RoutingEndpoints::updateDefaultConfigForProfile(),
            'updateDefaultConfigForProfile',
            ['profile_id' => $profileId],
            $request,
        );
    }

    /**
     * @openapi "Evaluate routing rules"
     *
     * @return array<int|string, mixed>
     */
    public function evaluate(RoutingEvaluateRequest $request): array
    {
        return $this->raw(RoutingEndpoints::evaluate(), 'evaluate', request: $request);
    }

    /**
     * @openapi "Update gateway scores"
     *
     * @return array<int|string, mixed>
     */
    public function feedback(UpdateScoreRequest $request): array
    {
        return $this->raw(RoutingEndpoints::feedback(), 'feedback', request: $request);
    }

    /**
     * @openapi "Evaluate routing rules (alternative)"
     *
     * @return array<int|string, mixed>
     */
    public function ruleEvaluate(RoutingEvaluateRequest $request): array
    {
        return $this->raw(RoutingEndpoints::ruleEvaluate(), 'ruleEvaluate', request: $request);
    }

    /** @openapi "Update success based dynamic routing configs" */
    public function updateSuccessBasedConfig(
        string $accountId,
        string $profileId,
        string $algorithmId,
        DynamicRoutingConfigRequest $request,
    ): RoutingDictionaryRecord {
        return $this->record(
            RoutingEndpoints::updateSuccessBasedConfig(),
            'updateSuccessBasedConfig',
            ['account_id' => $accountId, 'profile_id' => $profileId, 'algorithm_id' => $algorithmId],
            $request,
        );
    }

    /** @openapi "Update contract based dynamic routing configs" */
    public function updateContractConfig(
        string $accountId,
        string $profileId,
        string $algorithmId,
        DynamicRoutingConfigRequest $request,
    ): RoutingDictionaryRecord {
        return $this->record(
            RoutingEndpoints::updateContractConfig(),
            'updateContractConfig',
            ['account_id' => $accountId, 'profile_id' => $profileId, 'algorithm_id' => $algorithmId],
            $request,
        );
    }

    /** @openapi "Toggle contract routing algorithm" */
    public function toggleContracts(
        string $accountId,
        string $profileId,
        DynamicRoutingConfigRequest $request,
    ): RoutingDictionaryRecord {
        return $this->record(
            RoutingEndpoints::toggleContracts(),
            'toggleContracts',
            ['account_id' => $accountId, 'profile_id' => $profileId],
            $request,
        );
    }

    /** @openapi "Create elimination routing algorithm" */
    public function createElimination(
        string $accountId,
        string $profileId,
        DynamicRoutingConfigRequest $request,
    ): RoutingDictionaryRecord {
        return $this->record(
            RoutingEndpoints::createElimination(),
            'createElimination',
            ['account_id' => $accountId, 'profile_id' => $profileId],
            $request,
        );
    }

    /** @openapi "Toggle elimination routing algorithm" */
    public function toggleElimination(string $accountId, string $profileId, bool $enable): RoutingDictionaryRecord
    {
        return $this->record(
            RoutingEndpoints::toggleElimination(),
            'toggleElimination',
            ['account_id' => $accountId, 'profile_id' => $profileId],
            null,
            ['enable' => $enable ? 'true' : 'false'],
        );
    }

    /** @openapi "Create success based dynamic routing algorithm" */
    public function createSuccessBased(
        string $accountId,
        string $profileId,
        DynamicRoutingConfigRequest $request,
    ): RoutingDictionaryRecord {
        return $this->record(
            RoutingEndpoints::createSuccessBased(),
            'createSuccessBased',
            ['account_id' => $accountId, 'profile_id' => $profileId],
            $request,
        );
    }

    /** @openapi "Toggle success based dynamic routing algorithm" */
    public function toggleSuccessBased(string $accountId, string $profileId, bool $enable): RoutingDictionaryRecord
    {
        return $this->record(
            RoutingEndpoints::toggleSuccessBased(),
            'toggleSuccessBased',
            ['account_id' => $accountId, 'profile_id' => $profileId],
            null,
            ['enable' => $enable ? 'true' : 'false'],
        );
    }

    /**
     * @param array<string, string>                     $pathParams
     * @param array<string, scalar>                     $queryParams
     */
    private function record(
        Endpoint $endpoint,
        string $context,
        array $pathParams = [],
        ?\X2Y\HyperSwitchClient\Dto\AbstractRequestDto $request = null,
        array $queryParams = [],
    ): RoutingDictionaryRecord {
        $response = $this->execute($endpoint, request: $request, pathParams: $pathParams, queryParams: $queryParams);
        if (!$response instanceof RoutingDictionaryRecord) {
            throw new HyperSwitchApiException("Unexpected response type from Routing::{$context}.");
        }
        return $response;
    }

    /**
     * @param array<string, string> $pathParams
     * @return array<int|string, mixed>
     */
    private function raw(
        Endpoint $endpoint,
        string $context,
        array $pathParams = [],
        ?\X2Y\HyperSwitchClient\Dto\AbstractRequestDto $request = null,
    ): array {
        $response = $this->execute($endpoint, request: $request, pathParams: $pathParams);
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException("Unexpected response type from Routing::{$context}.");
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return $raw;
    }
}
