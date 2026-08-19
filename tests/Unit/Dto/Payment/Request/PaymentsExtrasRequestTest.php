<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Payment\Request;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Payment\Request\CompleteAuthorizePaymentRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\EligibilityCheckRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\EligibilityRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\ExternalAuthenticationRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\PostSessionTokensRequest;
use X2Y\HyperSwitchClient\Dto\Payment\Request\UpdateMetadataPaymentRequest;
use X2Y\HyperSwitchClient\Enum\DeviceChannel;
use X2Y\HyperSwitchClient\Enum\PaymentMethod;
use X2Y\HyperSwitchClient\Enum\ThreeDsCompletionIndicator;

final class PaymentsExtrasRequestTest extends TestCase
{
    public function testCompleteAuthorizeSerialisesEnumAndAddress(): void
    {
        $req = new CompleteAuthorizePaymentRequest(
            shipping: new Address(line1: '1 Main', city: 'SF', country: 'US'),
            clientSecret: 'pay_secret',
            threedsMethodCompInd: ThreeDsCompletionIndicator::COMPLETED,
        );
        self::assertSame([
            'shipping'                => ['line1' => '1 Main', 'city' => 'SF', 'country' => 'US'],
            'client_secret'           => 'pay_secret',
            'threeds_method_comp_ind' => 'Y',
        ], $req->toArray());
    }

    public function testCompleteAuthorizeEmptyIsEmpty(): void
    {
        self::assertSame([], (new CompleteAuthorizePaymentRequest())->toArray());
    }

    public function testExternalAuthenticationRequiresEnums(): void
    {
        $req = new ExternalAuthenticationRequest(
            deviceChannel: DeviceChannel::BROWSER,
            threedsMethodCompInd: ThreeDsCompletionIndicator::NOT_COMPLETED,
            clientSecret: 'cs',
            sdkInformation: ['sdk_app_id' => 'abc'],
        );
        self::assertSame([
            'device_channel'          => 'BRW',
            'threeds_method_comp_ind' => 'N',
            'client_secret'           => 'cs',
            'sdk_information'         => ['sdk_app_id' => 'abc'],
        ], $req->toArray());
    }

    public function testEligibilitySerialises(): void
    {
        $req = new EligibilityRequest(
            clientSecret: 'cs',
            paymentMethodType: PaymentMethod::WALLET,
            paymentToken: 'tok_1',
            paymentMethodSubtype: 'apple_pay',
        );
        self::assertSame([
            'client_secret'          => 'cs',
            'payment_method_type'    => 'wallet',
            'payment_token'          => 'tok_1',
            'payment_method_subtype' => 'apple_pay',
        ], $req->toArray());
    }

    public function testEligibilityCheckSerialises(): void
    {
        $req = new EligibilityCheckRequest(
            clientSecret: 'cs',
            paymentMethodType: PaymentMethod::CARD,
            paymentToken: 'tok_2',
        );
        self::assertSame([
            'client_secret'       => 'cs',
            'payment_method_type' => 'card',
            'payment_token'       => 'tok_2',
        ], $req->toArray());
    }

    public function testPostSessionTokensSerialises(): void
    {
        $req = new PostSessionTokensRequest(
            paymentMethod: PaymentMethod::WALLET,
            paymentMethodType: 'google_pay',
        );
        self::assertSame([
            'payment_method'      => 'wallet',
            'payment_method_type' => 'google_pay',
        ], $req->toArray());
    }

    public function testUpdateMetadataKeepsRequiredMetadataEvenWhenEmpty(): void
    {
        self::assertSame(['metadata' => []], (new UpdateMetadataPaymentRequest(metadata: []))->toArray());

        $req = new UpdateMetadataPaymentRequest(
            metadata: ['order_id' => '42'],
            featureMetadata: ['redirect' => ['url' => 'https://x']],
        );
        self::assertSame([
            'metadata'         => ['order_id' => '42'],
            'feature_metadata' => ['redirect' => ['url' => 'https://x']],
        ], $req->toArray());
    }
}
