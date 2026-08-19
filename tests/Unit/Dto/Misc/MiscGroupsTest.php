<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Misc;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Request\ToggleKvRequest;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Response\ToggleKvResponse;
use X2Y\HyperSwitchClient\Dto\Poll\Response\PollResponse;
use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Request\ThreeDsDecisionExecuteRequest;
use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Response\ThreeDsDecisionExecuteResponse;

final class MiscGroupsTest extends TestCase
{
    public function testToggleKv(): void
    {
        self::assertSame(['kv_enabled' => true], (new ToggleKvRequest(kvEnabled: true))->toArray());
        $r = ToggleKvResponse::fromArray(['merchant_id' => 'mer_1', 'kv_enabled' => false]);
        self::assertSame('mer_1', $r->merchantId);
        self::assertFalse($r->kvEnabled);
    }

    public function testPollResponse(): void
    {
        $r = PollResponse::fromArray(['poll_id' => 'poll_1', 'status' => 'completed']);
        self::assertSame('poll_1', $r->pollId);
        self::assertSame('completed', $r->status);
    }

    public function testThreeDsDecisionRoundTrip(): void
    {
        $req = new ThreeDsDecisionExecuteRequest(
            routingId: 'rt_1',
            payment: ['amount' => 100, 'currency' => 'USD'],
            issuer: ['name' => 'bank'],
        );
        $arr = $req->toArray();
        self::assertSame('rt_1', $arr['routing_id']);
        self::assertSame(['amount' => 100, 'currency' => 'USD'], $arr['payment']);
        self::assertSame(['name' => 'bank'], $arr['issuer']);

        $resp = ThreeDsDecisionExecuteResponse::fromArray(['decision' => 'challenge']);
        self::assertSame('challenge', $resp->decision);
    }
}
