<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Event;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Auth\JwtKeyAuthResolver;
use X2Y\HyperSwitchClient\Dto\Event\Request\EventListConstraintsRequest;
use X2Y\HyperSwitchClient\Dto\Event\Response\TotalEventsResponse;
use X2Y\HyperSwitchClient\Endpoint\EventsEndpoints;

final class EventsTest extends TestCase
{
    public function testJwtResolverBearerHeader(): void
    {
        $resolver = new JwtKeyAuthResolver('tok_123');
        self::assertSame(AuthTier::JWT, $resolver->tier());
        self::assertSame(['Authorization' => 'Bearer tok_123'], $resolver->headers());
    }

    public function testEventsUseJwtTier(): void
    {
        self::assertSame(AuthTier::JWT, EventsEndpoints::listForMerchant()->authTier);
        self::assertSame(AuthTier::JWT, EventsEndpoints::attempts()->authTier);
    }

    public function testListConstraints(): void
    {
        $req = new EventListConstraintsRequest(limit: 10, isDelivered: false, extras: ['object_id' => 'pay_1']);
        self::assertSame(['limit' => 10, 'is_delivered' => false, 'object_id' => 'pay_1'], $req->toArray());
    }

    public function testTotalEventsResponse(): void
    {
        $r = TotalEventsResponse::fromArray([
            'events'      => [['event_id' => 'ev_1'], ['event_id' => 'ev_2']],
            'total_count' => 2,
        ]);
        self::assertSame(2, $r->totalCount);
        self::assertCount(2, $r->events);
        self::assertSame('ev_1', $r->events[0]['event_id']);
    }
}
