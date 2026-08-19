<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\PaymentMethodV1;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request\CreatePaymentMethodV1Request;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Request\UpdatePaymentMethodV1Request;
use X2Y\HyperSwitchClient\Dto\PaymentMethodV1\Response\CustomerDefaultPaymentMethodResponse;
use X2Y\HyperSwitchClient\Endpoint\PaymentMethodsV1Endpoints;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;

final class PaymentMethodsV1Test extends TestCase
{
    public function testCreateRequestSerialises(): void
    {
        $req = new CreatePaymentMethodV1Request(
            paymentMethod: PaymentMethod::CARD,
            customerId: 'cus_1',
            paymentMethodType: 'credit',
            card: ['card_number' => '4111111111111111'],
        );
        self::assertSame([
            'payment_method'      => 'card',
            'customer_id'         => 'cus_1',
            'payment_method_type' => 'credit',
            'card'                => ['card_number' => '4111111111111111'],
        ], $req->toArray());
    }

    public function testUpdateRequestSerialises(): void
    {
        $req = new UpdatePaymentMethodV1Request(card: ['card_holder_name' => 'A B']);
        self::assertSame(['card' => ['card_holder_name' => 'A B']], $req->toArray());
    }

    public function testDefaultResponse(): void
    {
        $r = CustomerDefaultPaymentMethodResponse::fromArray([
            'customer_id'               => 'cus_1',
            'payment_method'            => 'card',
            'default_payment_method_id' => 'pm_9',
        ]);
        self::assertSame('cus_1', $r->customerId);
        self::assertSame('card', $r->paymentMethod);
        self::assertSame('pm_9', $r->defaultPaymentMethodId);
    }

    public function testSetDefaultEndpointUsesEphemeralAuthAndTwoParams(): void
    {
        $e = PaymentMethodsV1Endpoints::setDefault();
        self::assertSame('POST', $e->method);
        self::assertSame('/{customer_id}/payment_methods/{payment_method_id}/default', $e->pathTemplate);
        self::assertSame(AuthTier::EPHEMERAL, $e->authTier);
    }
}
