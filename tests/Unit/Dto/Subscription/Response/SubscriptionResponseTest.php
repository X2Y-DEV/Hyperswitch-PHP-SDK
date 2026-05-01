<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Dto\Subscription\Response;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Dto\Subscription\Response\SubscriptionResponse;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\SubscriptionStatus;

final class SubscriptionResponseTest extends TestCase
{
    public function testRequiredFields(): void
    {
        $s = SubscriptionResponse::fromArray([
            'subscription_id' => 'sub_42',
            'status'          => 'active',
        ]);
        self::assertSame('sub_42', $s->subscriptionId);
        self::assertSame(SubscriptionStatus::ACTIVE, $s->status);
    }

    public function testFallsBackFromIdToSubscriptionId(): void
    {
        $s = SubscriptionResponse::fromArray([
            'id'     => 'sub_alt',
            'status' => 'created',
        ]);
        self::assertSame('sub_alt', $s->subscriptionId);
    }

    public function testFullPayload(): void
    {
        $s = SubscriptionResponse::fromArray([
            'subscription_id'         => 'sub_42',
            'status'                  => 'active',
            'merchant_id'             => 'merchant_x',
            'customer_id'             => 'cus_42',
            'merchant_reference_id'   => 'merch_ref_1',
            'item_price_id'           => 'price_monthly',
            'plan_id'                 => 'plan_pro',
            'amount'                  => 4900,
            'currency'                => 'USD',
            'client_secret'           => 'sub_42_secret',
            'payment_id'              => 'pay_42',
            'current_term_start'      => '2026-04-01T00:00:00Z',
            'current_term_end'        => '2026-05-01T00:00:00Z',
            'next_billing_at'         => '2026-05-01T00:00:00Z',
            'created_at'              => '2026-04-01T00:00:00Z',
            'metadata'                => ['plan_tier' => 'pro'],
        ]);
        self::assertSame(4900, $s->amount);
        self::assertSame(Currency::USD, $s->currency);
        self::assertNotNull($s->currentTermStart);
        self::assertNotNull($s->nextBillingAt);
        self::assertSame(['plan_tier' => 'pro'], $s->metadata);
    }

    public function testTerminalStatus(): void
    {
        $cancelled = SubscriptionResponse::fromArray(['subscription_id' => 'sub_x', 'status' => 'cancelled']);
        self::assertTrue($cancelled->status->isTerminal());

        $active = SubscriptionResponse::fromArray(['subscription_id' => 'sub_x', 'status' => 'active']);
        self::assertFalse($active->status->isTerminal());
    }
}
