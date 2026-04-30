<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Endpoint;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\Proxy\Request\CreateProxyRequest;
use X2Y\HyperswitchClient\Dto\Proxy\Response\ProxyResponse;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * @postman             Proxy
 * @hyperswitch-source  2026-04-30
 */
final class ProxyEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Proxy / Proxy - Create
     * @endpoint  POST /v1/proxy
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/v1/proxy',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  CreateProxyRequest::class,
            responseClass: ProxyResponse::class,
        );
    }
}
