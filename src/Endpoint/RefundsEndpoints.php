<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Endpoint;

use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Refund\Request\CreateRefundRequest;
use X2Y\HyperSwitchClient\Dto\Refund\Request\ListRefundsRequest;
use X2Y\HyperSwitchClient\Dto\Refund\Request\UpdateRefundRequest;
use X2Y\HyperSwitchClient\Dto\Refund\Response\RefundResponse;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * HyperSwitch Refunds group.
 *
 * @hyperswitch-source  2026-04-30
 */
final class RefundsEndpoints
{
    private function __construct()
    {
    }

    /**
     * @endpoint  POST /refunds
     */
    public static function create(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/refunds',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreateRefundRequest::class,
            responseClass: RefundResponse::class,
        );
    }

    /**
     * @endpoint  POST /refunds/{id}
     */
    public static function update(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/refunds/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  UpdateRefundRequest::class,
            responseClass: RefundResponse::class,
        );
    }

    /**
     * @endpoint  GET /refunds/{id}
     */
    public static function retrieve(): Endpoint
    {
        return new Endpoint(
            method:        'GET',
            pathTemplate:  '/refunds/{id}',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: RefundResponse::class,
        );
    }

    /**
     * @endpoint  POST /refunds/list
     *
     * Returns a list shape (envelope with `data: [...]`) — modelled as a raw
     * decoded array on the Resource until a dedicated PaginatedList DTO lands.
     */
    public static function list(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/refunds/list',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  ListRefundsRequest::class,
            responseClass: null,
        );
    }
}
