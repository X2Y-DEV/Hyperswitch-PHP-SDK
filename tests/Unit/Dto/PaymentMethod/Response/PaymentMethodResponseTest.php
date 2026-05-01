<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Dto\PaymentMethod\Response;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Dto\PaymentMethod\Response\PaymentMethodResponse;
use X2Y\HyperswitchClient\Enum\PaymentMethod;

final class PaymentMethodResponseTest extends TestCase
{
    public function testRequiredField(): void
    {
        $pm = PaymentMethodResponse::fromArray(['payment_method_id' => 'pm_42']);
        self::assertSame('pm_42', $pm->paymentMethodId);
        self::assertNull($pm->paymentMethodType);
    }

    public function testIdFallback(): void
    {
        $pm = PaymentMethodResponse::fromArray(['id' => 'pm_alt']);
        self::assertSame('pm_alt', $pm->paymentMethodId);
    }

    public function testTypedPaymentMethodEnum(): void
    {
        $pm = PaymentMethodResponse::fromArray([
            'payment_method_id'      => 'pm_42',
            'customer_id'            => 'cus_42',
            'payment_method_type'    => 'card',
            'payment_method_subtype' => 'credit',
            'status'                 => 'active',
            'created_at'             => '2026-04-30T10:00:00Z',
        ]);
        self::assertSame(PaymentMethod::CARD, $pm->paymentMethodType);
        self::assertSame('credit', $pm->paymentMethodSubtype);
        self::assertNotNull($pm->createdAt);
    }

    public function testWalletType(): void
    {
        $pm = PaymentMethodResponse::fromArray([
            'payment_method_id'   => 'pm_w',
            'payment_method_type' => 'wallet',
        ]);
        self::assertSame(PaymentMethod::WALLET, $pm->paymentMethodType);
    }

    public function testUnknownFieldsLandInExtras(): void
    {
        $pm = PaymentMethodResponse::fromArray([
            'payment_method_id' => 'pm_x',
            'card'              => ['last4' => '4242'],   // not modelled; lives in extras
        ]);
        self::assertArrayHasKey('card', $pm->extras());
    }
}
