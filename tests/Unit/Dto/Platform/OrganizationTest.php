<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Platform;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Platform\Request\OrganizationUpdateRequest;
use X2Y\HyperSwitchClient\Dto\Platform\Response\OrganizationResponse;

final class OrganizationTest extends TestCase
{
    public function testUpdateRequestSerialises(): void
    {
        $req = new OrganizationUpdateRequest(
            platformMerchantId: 'mer_1',
            organizationName: 'Acme',
            metadata: ['tier' => 'gold'],
        );
        self::assertSame([
            'platform_merchant_id' => 'mer_1',
            'organization_name'    => 'Acme',
            'metadata'             => ['tier' => 'gold'],
        ], $req->toArray());
    }

    public function testResponseParsesRequiredAndDates(): void
    {
        $r = OrganizationResponse::fromArray([
            'organization_id'   => 'org_1',
            'created_at'        => '2026-01-01T00:00:00Z',
            'modified_at'       => '2026-02-01T00:00:00Z',
            'organization_name' => 'Acme',
            'metadata'          => ['k' => 'v'],
            'unknown'           => 1,
        ]);
        self::assertSame('org_1', $r->organizationId);
        self::assertNotNull($r->createdAt);
        self::assertSame(2026, (int) $r->createdAt->format('Y'));
        self::assertSame('Acme', $r->organizationName);
        self::assertSame(['k' => 'v'], $r->metadata);
        self::assertSame(1, $r->extra('unknown'));
    }
}
