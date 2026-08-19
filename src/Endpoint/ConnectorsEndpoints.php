<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Connector\Request\ConnectorWebhookRegisterRequest;
use X2Y\HyperSwitchClient\Dto\Connector\Request\CreateConnectorRequest;
use X2Y\HyperSwitchClient\Dto\Connector\Request\UpdateConnectorRequest;
use X2Y\HyperSwitchClient\Dto\Connector\Response\ConnectorDeleteResponse;
use X2Y\HyperSwitchClient\Dto\Connector\Response\ConnectorResponse;
use X2Y\HyperSwitchClient\Dto\Connector\Response\RegisterConnectorWebhookResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Payment Connectors group. Admin-tier — for attaching and
 * configuring upstream PSPs (Stripe, Adyen, etc.) on a merchant account.
 *
 * @postman             PaymentConnectors
 * @hyperswitch-source  2026-04-30
 */
final class ConnectorsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   PaymentConnectors / Payment Connector - Create
     * @endpoint  POST /account/{account_id}/connectors
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/connectors',
            authTier:      AuthTier::ADMIN,
            idempotent:    true,
            requestClass:  CreateConnectorRequest::class,
            responseClass: ConnectorResponse::class,
        );
    }

    /**
     * @postman   PaymentConnectors / Payment Connector - Retrieve
     * @endpoint  GET /account/{account_id}/connectors/{connector_id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/account/{account_id}/connectors/{connector_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: ConnectorResponse::class,
        );
    }

    /**
     * @postman   PaymentConnectors / Payment Connector - Update
     * @endpoint  POST /account/{account_id}/connectors/{connector_id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/connectors/{connector_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  UpdateConnectorRequest::class,
            responseClass: ConnectorResponse::class,
        );
    }

    /**
     * @postman   PaymentConnectors / List Connectors by MID
     * @endpoint  GET /account/{account_id}/connectors
     *
     * Resource wraps in PaginatedList<ConnectorResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/account/{account_id}/connectors',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   PaymentConnectors / Payment Connector - Delete
     * @endpoint  DELETE /account/{account_id}/connectors/{connector_id}
     */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/account/{account_id}/connectors/{connector_id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: ConnectorDeleteResponse::class,
        );
    }

    /**
     * @openapi   "Register a Connector Webhook"
     * @endpoint  POST /account/{account_id}/webhooks/{merchant_connector_id}
     */
    public static function registerWebhook(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/account/{account_id}/webhooks/{merchant_connector_id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  ConnectorWebhookRegisterRequest::class,
            responseClass: RegisterConnectorWebhookResponse::class,
        );
    }
}
