<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Webhook;

use X2Y\HyperswitchClient\Exception\InvalidSignatureException;
use X2Y\HyperswitchClient\Exception\InvalidWebhookPayloadException;
use X2Y\HyperswitchClient\Internal\Json;

/**
 * One-stop entry point for inbound Hyperswitch webhook handling.
 *
 *   $decoder = new EventDecoder(new SignatureVerifier($hashKey));
 *
 *   $body      = (string) $request->getBody();
 *   $signature = $request->getHeaderLine('X-Webhook-Signature-512');
 *   try {
 *       $event = $decoder->decode($body, $signature);
 *   } catch (InvalidSignatureException $e) {
 *       return new Response(401);            // do not echo $e->getMessage()
 *   } catch (InvalidWebhookPayloadException $e) {
 *       return new Response(400);
 *   }
 *
 *   match ($event->eventType) {
 *       EventType::PAYMENT_SUCCEEDED => $this->markOrderPaid($event->asPayment()),
 *       EventType::REFUND_SUCCEEDED  => $this->creditMemoFor($event->asRefund()),
 *       null                         => $this->logUnknown($event->eventTypeRaw),
 *       default                      => $this->logUnhandled($event),
 *   };
 *
 * Verification happens before JSON decode — an invalid signature short-
 * circuits without exposing the body to the JSON parser.
 */
final class EventDecoder
{
    public function __construct(
        private readonly SignatureVerifier $verifier,
    ) {
    }

    /**
     * Verify the signature, then decode the body into a typed WebhookEvent.
     *
     * @throws InvalidSignatureException        when the HMAC fails or the
     *         signature header is missing.
     * @throws InvalidWebhookPayloadException   when the verified body is
     *         not valid JSON, is not an object, or is missing `event_type`.
     */
    public function decode(string $body, string $signature): WebhookEvent
    {
        $this->verifier->verify($body, $signature);

        try {
            $envelope = Json::decode($body);
        } catch (\Throwable $e) {
            throw new InvalidWebhookPayloadException(
                'Webhook body is not valid JSON: ' . $e->getMessage(),
                0,
                $e,
            );
        }

        if (!isset($envelope['event_type']) || !is_string($envelope['event_type']) || $envelope['event_type'] === '') {
            throw new InvalidWebhookPayloadException(
                'Webhook envelope is missing the required `event_type` field.'
            );
        }

        return WebhookEvent::fromArray($envelope);
    }
}
