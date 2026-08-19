<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationActionRequest;
use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationCreateRequest;
use X2Y\HyperSwitchClient\Dto\Authentication\Response\AuthenticationResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Authentication group (external 3DS authentication lifecycle).
 *
 * @openapi             tag: Authentication
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class AuthenticationEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "Create an Authentication" @endpoint POST /authentication */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  AuthenticationCreateRequest::class,
            responseClass: AuthenticationResponse::class,
        );
    }

    /** @openapi "Authenticate an Authentication" @endpoint POST /authentication/{id}/authenticate */
    public static function authenticate(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication/{id}/authenticate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: AuthenticationResponse::class,
        );
    }

    /** @openapi "Check Authentication Eligibility" @endpoint POST /authentication/{id}/eligibility */
    public static function eligibility(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication/{id}/eligibility',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: AuthenticationResponse::class,
        );
    }

    /** @openapi "Retrieve Eligibility Check data for an Authentication" @endpoint GET /authentication/{id}/eligibility-check */
    public static function eligibilityCheckRetrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/authentication/{id}/eligibility-check',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Submit Eligibility for an Authentication" @endpoint POST /authentication/{id}/eligibility-check */
    public static function eligibilityCheckSubmit(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication/{id}/eligibility-check',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Enable Authentication Authn Methods Token" @endpoint POST /authentication/{id}/enabled_authn_methods_token */
    public static function sessionToken(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication/{id}/enabled_authn_methods_token',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Redirect an Authentication" @endpoint POST /authentication/{id}/redirect */
    public static function redirect(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication/{id}/redirect',
            authTier:      AuthTier::PUBLISHABLE,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: null,
        );
    }

    /** @openapi "Sync an Authentication" @endpoint POST /authentication/{id}/sync */
    public static function sync(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/authentication/{id}/sync',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  AuthenticationActionRequest::class,
            responseClass: AuthenticationResponse::class,
        );
    }
}
