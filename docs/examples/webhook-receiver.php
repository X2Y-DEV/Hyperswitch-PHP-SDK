<?php
/**
 * Minimal webhook receiver — bare PHP, no framework. Drop into a public
 * endpoint and point HyperSwitch's webhook configuration at this URL.
 *
 * For PSR-15 / framework integrations the same pattern applies — read the
 * raw body, read the signature header, decode, dispatch on event type.
 *
 * Configuration:
 *   - HYPERSWITCH_WEBHOOK_KEY  the merchant's "Payment Response Hash Key"
 *                              from HyperSwitch's business profile config.
 */

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use X2Y\HyperSwitchClient\Enum\EventType;
use X2Y\HyperSwitchClient\Exception\InvalidSignatureException;
use X2Y\HyperSwitchClient\Exception\InvalidWebhookPayloadException;
use X2Y\HyperSwitchClient\Webhook\EventDecoder;
use X2Y\HyperSwitchClient\Webhook\SignatureVerifier;

$hashKey = getenv('HYPERSWITCH_WEBHOOK_KEY');
if ($hashKey === false || $hashKey === '') {
    http_response_code(500);
    error_log('[hyperswitch-webhook] HYPERSWITCH_WEBHOOK_KEY is not configured');
    exit;
}

// Construct once; the verifier is stateless beyond key + algorithm.
$verifier = new SignatureVerifier($hashKey, SignatureVerifier::ALGO_SHA512);
$decoder  = new EventDecoder($verifier);

// HyperSwitch's signature header name has varied across versions. Check
// both common forms; case-insensitive.
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE_512']
    ?? $_SERVER['HTTP_X_WEBHOOK_SIGNATURE']
    ?? '';

$body = (string) file_get_contents('php://input');

try {
    $event = $decoder->decode($body, (string) $signature);
} catch (InvalidSignatureException $e) {
    // Do NOT echo $e->getMessage() — it may include canonical input.
    error_log('[hyperswitch-webhook] signature rejected');
    http_response_code(401);
    exit;
} catch (InvalidWebhookPayloadException $e) {
    error_log('[hyperswitch-webhook] payload rejected: ' . $e->getMessage());
    http_response_code(400);
    exit;
}

// At this point the body is authenticated. Idempotency: dedupe on event_id.
// (Real implementations should persist a "seen events" log to handle retries.)

switch ($event->eventType) {
    case EventType::PAYMENT_SUCCEEDED:
        $payment = $event->asPayment();
        if ($payment !== null) {
            error_log(sprintf(
                '[hyperswitch-webhook] payment %s succeeded amount=%d %s',
                $payment->paymentId,
                $payment->amount,
                $payment->currency->value,
            ));
            // your code: mark order paid, fulfill, etc.
        }
        break;

    case EventType::REFUND_SUCCEEDED:
        $refund = $event->asRefund();
        if ($refund !== null) {
            error_log(sprintf(
                '[hyperswitch-webhook] refund %s succeeded for payment %s',
                $refund->refundId,
                $refund->paymentId,
            ));
            // your code: create credit memo
        }
        break;

    case EventType::SUBSCRIPTION_RENEWAL_SUCCEEDED:
        $sub = $event->asSubscription();
        if ($sub !== null) {
            error_log(sprintf(
                '[hyperswitch-webhook] subscription %s renewed (status=%s)',
                $sub->subscriptionId,
                $sub->status->value,
            ));
            // your code: create new order from subscription
        }
        break;

    case null:
        // HyperSwitch shipped an event_type the SDK does not yet enumerate.
        // The raw string is preserved on $event->eventTypeRaw.
        error_log(sprintf(
            '[hyperswitch-webhook] unknown event_type "%s" (event_id=%s)',
            $event->eventTypeRaw,
            $event->eventId ?? 'n/a',
        ));
        break;

    default:
        // Known event type we don't currently handle — log and ack.
        error_log(sprintf(
            '[hyperswitch-webhook] unhandled %s (event_id=%s)',
            $event->eventType->value,
            $event->eventId ?? 'n/a',
        ));
}

// Always ack with 200 once authenticated, even for unhandled events —
// otherwise HyperSwitch will retry indefinitely.
http_response_code(200);
header('Content-Type: text/plain');
echo "OK\n";
