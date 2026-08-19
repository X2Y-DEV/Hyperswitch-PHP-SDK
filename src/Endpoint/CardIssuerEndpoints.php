<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Request\CardIssuerRequest;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Request\CardIssuerUpdateRequest;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Response\CardIssuerDeleteResponse;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Response\CardIssuerResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Card Issuer group.
 *
 * @openapi             tag: Card Issuer
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CardIssuerEndpoints
{
    private function __construct()
    {
    }

    /** @openapi "Create Card Issuer" @endpoint POST /card_issuers */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/card_issuers',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  CardIssuerRequest::class,
            responseClass: CardIssuerResponse::class,
        );
    }

    /** @openapi "List Card Issuers" @endpoint GET /card_issuers */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/card_issuers',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /** @openapi "Update Card Issuer" @endpoint PUT /card_issuers/{id} */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/card_issuers/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  CardIssuerUpdateRequest::class,
            responseClass: CardIssuerResponse::class,
        );
    }

    /** @openapi "Delete Card Issuer" @endpoint DELETE /card_issuers/{id} */
    public static function delete(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/card_issuers/{id}',
            authTier:      AuthTier::ADMIN,
            idempotent:    false,
            requestClass:  null,
            responseClass: CardIssuerDeleteResponse::class,
        );
    }
}
