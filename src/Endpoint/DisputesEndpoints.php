<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\AttachEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\DeleteEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\SubmitEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\CreateFileResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputeListFiltersResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputeResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputesAggregateResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Disputes group.
 *
 * @hyperswitch-source  2026-04-30
 */
final class DisputesEndpoints
{
    private function __construct()
    {
    }

    /**
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

    /**
     * @openapi   "Attach Evidence to Dispute"
     * @endpoint  PUT /disputes/evidence
     */
    public static function attachEvidence(): Endpoint
    {
        return new Endpoint(
            method:        'PUT',
            pathTemplate:  '/disputes/evidence',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  AttachEvidenceRequest::class,
            responseClass: CreateFileResponse::class,
        );
    }

    /**
     * @openapi   "Delete Evidence attached to a Dispute"
     * @endpoint  DELETE /disputes/evidence
     */
    public static function deleteEvidence(): Endpoint
    {
        return new Endpoint(
            method:        'DELETE',
            pathTemplate:  '/disputes/evidence',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  DeleteEvidenceRequest::class,
            responseClass: null,
        );
    }

    /**
     * @openapi   "Retrieve a Dispute Evidence"
     * @endpoint  GET /disputes/evidence/{id}
     *
     * Returns a bare array of evidence blocks; the Resource returns them raw.
     */
    public static function retrieveEvidence(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/evidence/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }

    /**
     * @openapi   "Get Disputes Aggregate"
     * @endpoint  GET /disputes/aggregate
     */
    public static function aggregate(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/aggregate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: DisputesAggregateResponse::class,
        );
    }

    /**
     * @openapi   "List all filters for disputes"
     * @endpoint  GET /disputes/filter
     */
    public static function filters(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/filter',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: DisputeListFiltersResponse::class,
        );
    }

    /**
     * @openapi   "Get Disputes Aggregate for Profiles"
     * @endpoint  GET /disputes/profile/aggregate
     */
    public static function profileAggregate(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/profile/aggregate',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: DisputesAggregateResponse::class,
        );
    }

    /**
     * @openapi   "List all filters for disputes for the given Profiles"
     * @endpoint  GET /disputes/profile/filter
     */
    public static function profileFilters(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/profile/filter',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: DisputeListFiltersResponse::class,
        );
    }

    /**
     * @openapi   "List Disputes for The given Profiles"
     * @endpoint  GET /disputes/profile/list
     *
     * Resource wraps in PaginatedList<DisputeResponse>.
     */
    public static function profileList(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/disputes/profile/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        );
    }
}
