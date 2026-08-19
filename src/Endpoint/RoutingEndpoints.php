<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Routing\Request\DynamicRoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\RoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\RoutingEvaluateRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\UpdateDefaultRoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\UpdateScoreRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Response\RoutingDictionaryRecord;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Routing group — routing configs, defaults, dynamic (success/
 * contract/elimination) routing, and rule evaluation. All merchant (api_key).
 *
 * Read-style endpoints return heterogeneous shapes; they declare
 * `responseClass: null` and the Resource returns the raw decoded payload.
 *
 * @openapi             tag: Routing
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class RoutingEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "List routing configs" @endpoint GET /routing */
    public static function list(): Endpoint
    {
        return new Endpoint(method: 'GET', pathTemplate: '/routing', authTier: AuthTier::MERCHANT);
    }

    /** @openapi "Retrieve a routing config" @endpoint GET /routing/{id} */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(method: 'GET', pathTemplate: '/routing/{id}', authTier: AuthTier::MERCHANT);
    }

    /** @openapi "Retrieve active config" @endpoint GET /routing/active */
    public static function active(): Endpoint
    {
        return new Endpoint(method: 'GET', pathTemplate: '/routing/active', authTier: AuthTier::MERCHANT);
    }

    /** @openapi "Retrieve default fallback config" @endpoint GET /routing/default */
    public static function defaultConfig(): Endpoint
    {
        return new Endpoint(method: 'GET', pathTemplate: '/routing/default', authTier: AuthTier::MERCHANT);
    }

    /** @openapi "Retrieve default configs for all profiles" @endpoint GET /routing/default/profile */
    public static function defaultConfigForProfiles(): Endpoint
    {
        return new Endpoint(method: 'GET', pathTemplate: '/routing/default/profile', authTier: AuthTier::MERCHANT);
    }

    /** @openapi "Create a routing config" @endpoint POST /routing */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  RoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Activate a routing config" @endpoint POST /routing/{id}/activate */
    public static function activate(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/{id}/activate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Deactivate a routing config" @endpoint POST /routing/deactivate */
    public static function deactivate(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/deactivate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  RoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Update default fallback config" @endpoint POST /routing/default */
    public static function updateDefaultConfig(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/default',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateDefaultRoutingConfigRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Update default configs for all profiles" @endpoint POST /routing/default/profile/{id} */
    public static function updateDefaultConfigForProfile(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/default/profile/{profile_id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateDefaultRoutingConfigRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Evaluate routing rules" @endpoint POST /routing/evaluate */
    public static function evaluate(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/evaluate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  RoutingEvaluateRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Update gateway scores" @endpoint POST /routing/feedback */
    public static function feedback(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/feedback',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateScoreRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Evaluate routing rules (alternative)" @endpoint POST /routing/rule/evaluate */
    public static function ruleEvaluate(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/routing/rule/evaluate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  RoutingEvaluateRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Update success based dynamic routing configs" @endpoint PATCH /account/{id}/business_profile/{id}/dynamic_routing/success_based/config/{id} */
    public static function updateSuccessBasedConfig(): Endpoint
    {
        return new Endpoint(
            method:        'PATCH',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/success_based/config/{algorithm_id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  DynamicRoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Update contract based dynamic routing configs" @endpoint PATCH /account/{id}/business_profile/{id}/dynamic_routing/contracts/config/{id} */
    public static function updateContractConfig(): Endpoint
    {
        return new Endpoint(
            method:        'PATCH',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/contracts/config/{algorithm_id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  DynamicRoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Toggle contract routing algorithm" @endpoint POST /account/{id}/business_profile/{id}/dynamic_routing/contracts/toggle */
    public static function toggleContracts(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/contracts/toggle',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  DynamicRoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Create elimination routing algorithm" @endpoint POST /account/{id}/business_profile/{id}/dynamic_routing/elimination/create */
    public static function createElimination(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/elimination/create',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  DynamicRoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Toggle elimination routing algorithm" @endpoint POST /account/{id}/business_profile/{id}/dynamic_routing/elimination/toggle */
    public static function toggleElimination(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/elimination/toggle',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Create success based dynamic routing algorithm" @endpoint POST /account/{id}/business_profile/{id}/dynamic_routing/success_based/create */
    public static function createSuccessBased(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/success_based/create',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  DynamicRoutingConfigRequest::class,
            responseClass: RoutingDictionaryRecord::class,
        );
    }

    /** @openapi "Toggle success based dynamic routing algorithm" @endpoint POST /account/{id}/business_profile/{id}/dynamic_routing/success_based/toggle */
    public static function toggleSuccessBased(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/success_based/toggle',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: RoutingDictionaryRecord::class,
        );
    }
}
