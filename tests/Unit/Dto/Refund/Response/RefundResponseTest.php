<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Refund\Response;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Refund\Response\RefundResponse;
use X2Y\HyperSwitchClient\Enum\Currency;
use X2Y\HyperSwitchClient\Enum\RefundStatus;
use X2Y\HyperSwitchClient\Enum\RefundType;

final class RefundResponseTest extends TestCase
{
    public function testRequiredFields(): void
    {
        $r = RefundResponse::fromArray([
            'refund_id'  => 'ref_x',
            'payment_id' => 'pay_x',
            'status'     => 'succeeded',
            'amount'     => 600,
            'currency'   => 'USD',
        ]);
        self::assertSame('ref_x', $r->refundId);
        self::assertSame(RefundStatus::SUCCEEDED, $r->status);
        self::assertTrue($r->status->isTerminal());
        self::assertSame(Currency::USD, $r->currency);
    }

    public function testOptionalFields(): void
    {
        $r = RefundResponse::fromArray([
            'refund_id'             => 'ref_x',
            'payment_id'            => 'pay_x',
            'status'                => 'pending',
            'amount'                => 100,
            'currency'              => 'USD',
            'refund_type'           => 'instant',
            'reason'                => 'Customer returned product',
            'created_at'            => '2026-04-30T10:00:00Z',
            'updated_at'            => '2026-04-30T10:01:00Z',
            'connector'             => 'stripe',
            'profile_id'            => 'pro_42',
            'merchant_connector_id' => 'mca_42',
            'metadata'              => ['note' => 'partial'],
        ]);
        self::assertSame(RefundType::INSTANT, $r->refundType);
        self::assertSame('Customer returned product', $r->reason);
        self::assertSame('stripe', $r->connector);
        self::assertSame(['note' => 'partial'], $r->metadata);
        self::assertNotNull($r->createdAt);
    }

    public function testNonTerminalStatusIsCorrectlyDetected(): void
    {
        $r = RefundResponse::fromArray([
            'refund_id'  => 'ref_x',
            'payment_id' => 'pay_x',
            'status'     => 'pending',
            'amount'     => 100,
            'currency'   => 'USD',
        ]);
        self::assertFalse($r->status->isTerminal());
    }

    public function testExtrasPreservation(): void
    {
        $r = RefundResponse::fromArray([
            'refund_id'  => 'ref_x',
            'payment_id' => 'pay_x',
            'status'     => 'succeeded',
            'amount'     => 100,
            'currency'   => 'USD',
            'attempt_id' => 'att_42',
        ]);
        self::assertSame('att_42', $r->extra('attempt_id'));
    }
}
