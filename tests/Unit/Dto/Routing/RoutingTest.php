<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Routing;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Routing\Request\RoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Request\UpdateDefaultRoutingConfigRequest;
use X2Y\HyperSwitchClient\Dto\Routing\Response\RoutingDictionaryRecord;
use X2Y\HyperSwitchClient\Endpoint\RoutingEndpoints;

final class RoutingTest extends TestCase
{
    public function testConfigRequestSerialises(): void
    {
        $req = new RoutingConfigRequest(
            name: 'primary',
            description: 'main',
            algorithm: ['type' => 'priority', 'data' => ['connectors' => ['stripe']]],
        );
        self::assertSame([
            'name'        => 'primary',
            'description' => 'main',
            'algorithm'   => ['type' => 'priority', 'data' => ['connectors' => ['stripe']]],
        ], $req->toArray());
    }

    public function testDefaultConfigRequestIsTopLevelArray(): void
    {
        $req = new UpdateDefaultRoutingConfigRequest([
            ['connector' => 'stripe'],
            ['connector' => 'adyen'],
        ]);
        self::assertSame([
            ['connector' => 'stripe'],
            ['connector' => 'adyen'],
        ], $req->toArray());
        self::assertTrue(array_is_list($req->toArray()));
    }

    public function testDictionaryRecordResponse(): void
    {
        $r = RoutingDictionaryRecord::fromArray([
            'id'          => 'rt_1',
            'profile_id'  => 'pro_1',
            'name'        => 'primary',
            'kind'        => 'single',
            'description' => 'main',
            'created_at'  => '2026-01-01T00:00:00Z',
            'modified_at' => '2026-01-02T00:00:00Z',
        ]);
        self::assertSame('rt_1', $r->id);
        self::assertSame('pro_1', $r->profileId);
        self::assertSame('single', $r->kind);
        self::assertNotNull($r->createdAt);
    }

    public function testDynamicRoutingEndpointShape(): void
    {
        $e = RoutingEndpoints::updateSuccessBasedConfig();
        self::assertSame('PATCH', $e->method);
        self::assertSame(
            '/account/{account_id}/business_profile/{profile_id}/dynamic_routing/success_based/config/{algorithm_id}',
            $e->pathTemplate,
        );
        self::assertSame(AuthTier::MERCHANT, $e->authTier);
    }
}
