<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Payment\Response;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Payment\Response\EligibilityCheckResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\EligibilityResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\ExternalAuthenticationResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\PostSessionTokensResponse;
use X2Y\HyperSwitchClient\Dto\Payment\Response\UpdateMetadataPaymentResponse;
use X2Y\HyperSwitchClient\Enum\IntentStatus;
use X2Y\HyperSwitchClient\Enum\TransactionStatus;

final class PaymentsExtrasResponseTest extends TestCase
{
    public function testExternalAuthenticationResponse(): void
    {
        $r = ExternalAuthenticationResponse::fromArray([
            'trans_status'           => 'C',
            'three_ds_requestor_url' => 'https://requestor',
            'acs_url'                => 'https://acs',
            'unmapped'               => 'x',
        ]);
        self::assertSame(TransactionStatus::CHALLENGE_REQUIRED, $r->transStatus);
        self::assertSame('https://requestor', $r->threeDsRequestorUrl);
        self::assertSame('https://acs', $r->acsUrl);
        self::assertNull($r->errorMessage);
        self::assertSame('x', $r->extra('unmapped'));
    }

    public function testEligibilityResponseNestedObjects(): void
    {
        $r = EligibilityResponse::fromArray([
            'payment_id'      => 'pay_1',
            'sdk_next_action' => ['next_action' => 'confirm'],
            'offer_details'   => ['offers' => []],
        ]);
        self::assertSame('pay_1', $r->paymentId);
        self::assertSame(['next_action' => 'confirm'], $r->sdkNextAction);
        self::assertSame(['offers' => []], $r->offerDetails);
        self::assertNull($r->surchargeDetails);
    }

    public function testEligibilityCheckResponse(): void
    {
        $r = EligibilityCheckResponse::fromArray(['payment_id' => 'pay_2']);
        self::assertSame('pay_2', $r->paymentId);
        self::assertNull($r->sdkNextAction);
    }

    public function testPostSessionTokensResponse(): void
    {
        $r = PostSessionTokensResponse::fromArray([
            'payment_id'  => 'pay_3',
            'status'      => 'requires_customer_action',
            'next_action' => ['type' => 'redirect'],
        ]);
        self::assertSame('pay_3', $r->paymentId);
        self::assertSame(IntentStatus::REQUIRES_CUSTOMER_ACTION, $r->status);
        self::assertSame(['type' => 'redirect'], $r->nextAction);
    }

    public function testUpdateMetadataResponse(): void
    {
        $r = UpdateMetadataPaymentResponse::fromArray([
            'payment_id' => 'pay_4',
            'metadata'   => ['k' => 'v'],
            'status'     => 'succeeded',
        ]);
        self::assertSame('pay_4', $r->paymentId);
        self::assertSame(['k' => 'v'], $r->metadata);
        self::assertSame(IntentStatus::SUCCEEDED, $r->status);
        self::assertNull($r->featureMetadata);
    }
}
