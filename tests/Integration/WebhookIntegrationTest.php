<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Integration;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Enum\EventType;
use X2Y\HyperSwitchClient\Webhook\EventDecoder;
use X2Y\HyperSwitchClient\Webhook\SignatureVerifier;

/**
 * Round-trip the webhook signing/decoding pipeline end-to-end against a
 * real merchant hash key. No network call — but uses the actual hash key
 * value the merchant has on file, so a regression in the hash algorithm
 * or header expectations would surface here even before pointing
 * HyperSwitch's webhook delivery at the SDK.
 *
 * Skipped when `HYPERSWITCH_WEBHOOK_KEY` is not set. Note this is a
 * separate env var from `HYPERSWITCH_SANDBOX_KEY` because webhook keys
 * (the "Payment Response Hash Key" in HyperSwitch's dashboard) are not
 * the same value as the merchant API key.
 */
final class WebhookIntegrationTest extends TestCase
{
    private SignatureVerifier $verifier;
    private EventDecoder      $decoder;

    protected function setUp(): void
    {
        $key = getenv('HYPERSWITCH_WEBHOOK_KEY');
        if ($key === false || $key === '') {
            self::markTestSkipped('HYPERSWITCH_WEBHOOK_KEY is not set; skipping webhook integration test.');
        }
        $this->verifier = new SignatureVerifier($key);
        $this->decoder  = new EventDecoder($this->verifier);
    }

    public function testRoundTripPaymentSucceededShape(): void
    {
        // Simulated HyperSwitch payment.succeeded envelope using the live key.
        $body = (string) json_encode([
            'merchant_id' => 'merchant_integration',
            'event_id'    => 'evt_int_001',
            'event_type'  => 'payment_succeeded',
            'timestamp'   => '2026-04-30T20:00:00Z',
            'content'     => [
                'object' => [
                    'payment_id' => 'pay_int_001',
                    'status'     => 'succeeded',
                    'amount'     => 4500,
                    'currency'   => 'USD',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $signature = $this->verifier->sign($body);
        $event     = $this->decoder->decode($body, $signature);

        self::assertSame(EventType::PAYMENT_SUCCEEDED, $event->eventType);
        self::assertSame('pay_int_001', $event->asPayment()?->paymentId);
    }

    public function testTamperedBodyRejected(): void
    {
        $body = (string) json_encode(['event_type' => 'payment_succeeded'], JSON_THROW_ON_ERROR);
        $sig  = $this->verifier->sign($body);

        $tampered = (string) json_encode(['event_type' => 'refund_succeeded'], JSON_THROW_ON_ERROR);

        $this->expectException(\X2Y\HyperSwitchClient\Exception\InvalidSignatureException::class);
        $this->decoder->decode($tampered, $sig);
    }
}
