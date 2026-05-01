<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Webhook;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Enum\EventType;
use X2Y\HyperswitchClient\Exception\InvalidSignatureException;
use X2Y\HyperswitchClient\Exception\InvalidWebhookPayloadException;
use X2Y\HyperswitchClient\Webhook\EventDecoder;
use X2Y\HyperswitchClient\Webhook\SignatureVerifier;

final class EventDecoderTest extends TestCase
{
    private const KEY = 'jP3uE7c9UhXBg8uGWlpuexqNtFXi2y10';

    private SignatureVerifier $verifier;
    private EventDecoder      $decoder;

    protected function setUp(): void
    {
        $this->verifier = new SignatureVerifier(self::KEY);
        $this->decoder  = new EventDecoder($this->verifier);
    }

    public function testDecodesPaymentSucceededEnvelope(): void
    {
        $body = json_encode([
            'merchant_id' => 'merchant_42',
            'event_id'    => 'evt_abc',
            'event_type'  => 'payment_succeeded',
            'timestamp'   => '2026-04-30T12:00:00Z',
            'content'     => [
                'object' => [
                    'payment_id' => 'pay_abc123',
                    'status'     => 'succeeded',
                    'amount'     => 4500,
                    'currency'   => 'USD',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $event = $this->decoder->decode($body, $this->verifier->sign($body));

        self::assertSame('payment_succeeded', $event->eventTypeRaw);
        self::assertSame(EventType::PAYMENT_SUCCEEDED, $event->eventType);
        self::assertSame('evt_abc', $event->eventId);
        self::assertSame('merchant_42', $event->merchantId);
        self::assertNotNull($event->timestamp);

        $payment = $event->asPayment();
        self::assertNotNull($payment);
        self::assertSame('pay_abc123', $payment->paymentId);
        self::assertSame(4500, $payment->amount);
    }

    public function testFlatTopLevelPayloadIsNotMisinterpreted(): void
    {
        // Hyperswitch occasionally ships the resource at the top level
        // alongside event_type. We extract from content/data when present;
        // otherwise the payload is empty and asPayment() returns null.
        $body = json_encode([
            'event_type' => 'payment_processing',
        ], JSON_THROW_ON_ERROR);

        $event = $this->decoder->decode($body, $this->verifier->sign($body));
        self::assertSame(EventType::PAYMENT_PROCESSING, $event->eventType);
        self::assertSame([], $event->payload);
        self::assertNull($event->asPayment());
    }

    public function testDataKeyAcceptedAsPayloadFallback(): void
    {
        $body = json_encode([
            'event_type' => 'refund_succeeded',
            'data'       => [
                'refund_id'  => 'ref_xyz',
                'payment_id' => 'pay_abc',
                'status'     => 'succeeded',
                'amount'     => 1000,
                'currency'   => 'USD',
            ],
        ], JSON_THROW_ON_ERROR);

        $event = $this->decoder->decode($body, $this->verifier->sign($body));
        $refund = $event->asRefund();
        self::assertNotNull($refund);
        self::assertSame('ref_xyz', $refund->refundId);
    }

    public function testUnknownEventTypeKeepsRawAndReturnsNullEnum(): void
    {
        $body = json_encode([
            'event_type' => 'something_brand_new',
            'content'    => ['object' => []],
        ], JSON_THROW_ON_ERROR);

        $event = $this->decoder->decode($body, $this->verifier->sign($body));
        self::assertSame('something_brand_new', $event->eventTypeRaw);
        self::assertNull($event->eventType);
    }

    public function testInvalidSignatureRejected(): void
    {
        $body = json_encode(['event_type' => 'payment_succeeded'], JSON_THROW_ON_ERROR);

        $this->expectException(InvalidSignatureException::class);
        $this->decoder->decode($body, str_repeat('0', 128));
    }

    public function testNonJsonBodyRejectedAfterSignaturePass(): void
    {
        $body = 'not json at all';
        $sig  = $this->verifier->sign($body);

        $this->expectException(InvalidWebhookPayloadException::class);
        $this->expectExceptionMessage('not valid JSON');
        $this->decoder->decode($body, $sig);
    }

    public function testMissingEventTypeRejected(): void
    {
        $body = json_encode(['content' => ['object' => []]], JSON_THROW_ON_ERROR);
        $sig  = $this->verifier->sign($body);

        $this->expectException(InvalidWebhookPayloadException::class);
        $this->expectExceptionMessage('event_type');
        $this->decoder->decode($body, $sig);
    }

    public function testEmptyEventTypeRejected(): void
    {
        $body = json_encode(['event_type' => ''], JSON_THROW_ON_ERROR);
        $sig  = $this->verifier->sign($body);

        $this->expectException(InvalidWebhookPayloadException::class);
        $this->decoder->decode($body, $sig);
    }

    public function testUnknownEnvelopeFieldsLandInExtras(): void
    {
        $body = json_encode([
            'event_type' => 'payment_succeeded',
            'content'    => ['object' => ['payment_id' => 'pay_x', 'status' => 'succeeded', 'amount' => 1, 'currency' => 'USD']],
            'tenant_id'  => 'tenant_42',
        ], JSON_THROW_ON_ERROR);

        $event = $this->decoder->decode($body, $this->verifier->sign($body));
        self::assertArrayHasKey('tenant_id', $event->extras);
        self::assertSame('tenant_42', $event->extras['tenant_id']);
    }
}
