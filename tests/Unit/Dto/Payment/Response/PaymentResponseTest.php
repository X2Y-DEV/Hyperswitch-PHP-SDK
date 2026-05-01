<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Dto\Payment\Response;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Enum\CaptureMethod;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\IntentStatus;

final class PaymentResponseTest extends TestCase
{
    public function testRequiredFieldsTyped(): void
    {
        $r = PaymentResponse::fromArray([
            'payment_id' => 'pay_abc',
            'status'     => 'requires_payment_method',
            'amount'     => 4500,
            'currency'   => 'USD',
        ]);
        self::assertSame('pay_abc', $r->paymentId);
        self::assertSame(IntentStatus::REQUIRES_PAYMENT_METHOD, $r->status);
        self::assertSame(4500, $r->amount);
        self::assertSame(Currency::USD, $r->currency);
    }

    public function testOptionalFieldsTyped(): void
    {
        $r = PaymentResponse::fromArray([
            'payment_id'        => 'pay_abc',
            'status'            => 'requires_capture',
            'amount'            => 4500,
            'currency'          => 'USD',
            'client_secret'     => 'pay_abc_secret_xyz',
            'customer_id'       => 'cus_42',
            'amount_capturable' => 4500,
            'amount_received'   => 0,
            'capture_method'    => 'manual',
            'created'           => '2026-04-30T10:00:00Z',
            'description'       => 'Test',
            'return_url'        => 'https://example.com/return',
        ]);
        self::assertSame('pay_abc_secret_xyz', $r->clientSecret);
        self::assertSame('cus_42', $r->customerId);
        self::assertSame(4500, $r->amountCapturable);
        self::assertSame(0, $r->amountReceived);
        self::assertSame(CaptureMethod::MANUAL, $r->captureMethod);
        self::assertNotNull($r->created);
        self::assertSame(2026, (int) $r->created->format('Y'));
    }

    public function testUnknownFieldsLandInExtras(): void
    {
        $r = PaymentResponse::fromArray([
            'payment_id' => 'pay_x',
            'status'     => 'succeeded',
            'amount'     => 100,
            'currency'   => 'USD',
            'attempts'   => [],     // not modelled
            'tenant_id'  => 'xyz',  // not modelled
        ]);
        self::assertArrayHasKey('attempts', $r->extras());
        self::assertSame('xyz', $r->extra('tenant_id'));
    }

    public function testIntentStatusTerminalDetection(): void
    {
        $succeeded = PaymentResponse::fromArray(['payment_id' => 'p', 'status' => 'succeeded', 'amount' => 1, 'currency' => 'USD']);
        self::assertTrue($succeeded->status->isTerminal());

        $pending = PaymentResponse::fromArray(['payment_id' => 'p', 'status' => 'requires_payment_method', 'amount' => 1, 'currency' => 'USD']);
        self::assertFalse($pending->status->isTerminal());
    }
}
