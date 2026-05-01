<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Transport;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Transport\Endpoint;

final class EndpointTest extends TestCase
{
    public function testStoresFields(): void
    {
        $e = new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  null,
            responseClass: null,
        );
        self::assertSame('POST', $e->method);
        self::assertSame('/payments', $e->pathTemplate);
        self::assertSame(AuthTier::MERCHANT, $e->authTier);
        self::assertTrue($e->idempotent);
    }

    public function testRequiresIdempotencyKeyOnIdempotentMutations(): void
    {
        foreach (['POST', 'PUT', 'PATCH'] as $method) {
            $e = new Endpoint(
                method:       $method,
                pathTemplate: '/x',
                authTier:     AuthTier::MERCHANT,
                idempotent:   true,
            );
            self::assertTrue($e->requiresIdempotencyKey(), "{$method} should require idempotency");
        }
    }

    public function testRequiresIdempotencyKeyFalseWhenNotIdempotent(): void
    {
        $e = new Endpoint(
            method:       'POST',
            pathTemplate: '/x',
            authTier:     AuthTier::MERCHANT,
            idempotent:   false,
        );
        self::assertFalse($e->requiresIdempotencyKey());
    }

    public function testRequiresIdempotencyKeyFalseForGetEvenWhenIdempotentFlagSet(): void
    {
        // Defensive: idempotency-key headers only make sense on mutations.
        $e = new Endpoint(
            method:       'GET',
            pathTemplate: '/x',
            authTier:     AuthTier::MERCHANT,
            idempotent:   true,
        );
        self::assertFalse($e->requiresIdempotencyKey());
    }

    public function testRequiresIdempotencyKeyFalseForDelete(): void
    {
        $e = new Endpoint(
            method:       'DELETE',
            pathTemplate: '/x',
            authTier:     AuthTier::MERCHANT,
            idempotent:   true,
        );
        self::assertFalse($e->requiresIdempotencyKey());
    }
}
