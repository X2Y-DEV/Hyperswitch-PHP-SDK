<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Auth\AdminKeyAuthResolver;
use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Auth\EphemeralKeyAuthResolver;
use X2Y\HyperswitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperswitchClient\Auth\PublishableKeyAuthResolver;

final class AuthResolversTest extends TestCase
{
    public function testMerchantKeyResolver(): void
    {
        $r = new MerchantKeyAuthResolver('snd_merchant_xxx');
        self::assertSame(AuthTier::MERCHANT, $r->tier());
        self::assertSame(['api-key' => 'snd_merchant_xxx'], $r->headers());
    }

    public function testAdminKeyResolver(): void
    {
        $r = new AdminKeyAuthResolver('admin_xxx');
        self::assertSame(AuthTier::ADMIN, $r->tier());
        self::assertSame(['api-key' => 'admin_xxx'], $r->headers());
    }

    public function testPublishableKeyResolver(): void
    {
        $r = new PublishableKeyAuthResolver('pk_snd_xxx');
        self::assertSame(AuthTier::PUBLISHABLE, $r->tier());
        self::assertSame(['api-key' => 'pk_snd_xxx'], $r->headers());
    }

    public function testEphemeralKeyResolver(): void
    {
        $r = new EphemeralKeyAuthResolver('eki_xxx');
        self::assertSame(AuthTier::EPHEMERAL, $r->tier());
        self::assertSame(['api-key' => 'eki_xxx'], $r->headers());
    }

    public function testTiersAreDistinct(): void
    {
        $tiers = [
            (new MerchantKeyAuthResolver('m'))->tier(),
            (new AdminKeyAuthResolver('a'))->tier(),
            (new PublishableKeyAuthResolver('p'))->tier(),
            (new EphemeralKeyAuthResolver('e'))->tier(),
        ];
        self::assertCount(4, array_unique(array_map(static fn (AuthTier $t): string => $t->value, $tiers)));
    }
}
