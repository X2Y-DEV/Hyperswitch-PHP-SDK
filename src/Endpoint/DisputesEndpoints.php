<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Endpoint;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\Dispute\Request\SubmitEvidenceRequest;
use X2Y\HyperswitchClient\Dto\Dispute\Response\DisputeResponse;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * Hyperswitch Disputes group.
 *
 * @postman             Disputes
 * @hyperswitch-source  2026-04-30
 */
final class DisputesEndpoints
{
    private function __construct()
    {
    }

    /**
     * @postman   Disputes / Disputes - Retrieve
     * @endpoint  GET /disputes/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: DisputeResponse::class,
        );
    }

    /**
     * @postman   Disputes / Dispute - List
     * @endpoint  GET /disputes/list
     *
     * Resource wraps in PaginatedList<DisputeResponse>.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @postman   Disputes / Disputes - Accept
     * @endpoint  POST /disputes/accept/{id}
     */
    public static function accept(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/disputes/accept/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: DisputeResponse::class,
        );
    }

    /**
     * @postman   Disputes / Disputes - Submit Evidence
     * @endpoint  POST /disputes/evidence
     */
    public static function submitEvidence(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/disputes/evidence',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  SubmitEvidenceRequest::class,
            responseClass: DisputeResponse::class,
        );
    }
}
