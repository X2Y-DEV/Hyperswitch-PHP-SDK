<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Webhook;

use X2Y\HyperswitchClient\Dto\Customer\Response\CustomerResponse;
use X2Y\HyperswitchClient\Dto\Dispute\Response\DisputeResponse;
use X2Y\HyperswitchClient\Dto\Mandate\Response\MandateResponse;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Dto\Payout\Response\PayoutResponse;
use X2Y\HyperswitchClient\Dto\Refund\Response\RefundResponse;
use X2Y\HyperswitchClient\Dto\Subscription\Response\SubscriptionResponse;
use X2Y\HyperswitchClient\Enum\EventType;
use X2Y\HyperswitchClient\Internal\DateTimeParser;

/**
 * Verified inbound webhook event.
 *
 * The envelope shape Hyperswitch emits is approximately:
 *
 *     {
 *       "merchant_id":  "...",
 *       "event_id":     "evt_...",
 *       "event_type":   "payment_succeeded",
 *       "content":      { "object": { ... resource state ... } },
 *       "timestamp":    "2026-04-30T12:34:56.789Z"
 *     }
 *
 * Hyperswitch occasionally tweaks the envelope (e.g. `data` instead of
 * `content`, flat resource at the top level instead of nested under
 * `content.object`) — we accept both. Anything not modelled goes to
 * `extras` so consumers don't lose forward compatibility.
 *
 * `eventType` is an enum where possible (`tryFrom` lookup); `eventTypeRaw`
 * is the underlying string and is always populated, so consumers can
 * dispatch on event types the SDK has not yet enumerated.
 */
final class WebhookEvent implements WebhookEventInterface
{
    /**
     * @param array<string, mixed> $payload  resource state extracted from `content.object` / `data` / top-level
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $eventTypeRaw,
        public readonly array $payload,
        public readonly ?EventType $eventType = null,
        public readonly ?string $eventId = null,
        public readonly ?string $merchantId = null,
        public readonly ?\DateTimeImmutable $timestamp = null,
        public readonly array $extras = [],
    ) {
    }

    /**
     * Decode an already-validated JSON envelope.
     *
     * @param array<string, mixed> $envelope
     */
    public static function fromArray(array $envelope): self
    {
        $eventTypeRaw = (string) ($envelope['event_type'] ?? '');

        // The resource payload may be nested under `content.object`, under
        // `content`, under `data`, or at the top level. Try each.
        $payload = self::extractPayload($envelope);

        $known = ['event_type', 'event_id', 'merchant_id', 'timestamp', 'content', 'data', 'object'];

        return new self(
            eventTypeRaw: $eventTypeRaw,
            payload:      $payload,
            eventType:    $eventTypeRaw !== '' ? EventType::tryFrom($eventTypeRaw) : null,
            eventId:      isset($envelope['event_id'])    ? (string) $envelope['event_id']    : null,
            merchantId:   isset($envelope['merchant_id']) ? (string) $envelope['merchant_id'] : null,
            timestamp:    DateTimeParser::parse($envelope['timestamp'] ?? null),
            extras:       array_diff_key($envelope, array_flip($known)),
        );
    }

    /**
     * @param array<string, mixed> $envelope
     * @return array<string, mixed>
     */
    private static function extractPayload(array $envelope): array
    {
        // content.object → content → data → top-level fallback
        $content = $envelope['content'] ?? null;
        if (is_array($content)) {
            $object = $content['object'] ?? null;
            if (is_array($object)) {
                /** @var array<string, mixed> $object */
                return $object;
            }
            /** @var array<string, mixed> $content */
            return $content;
        }
        $data = $envelope['data'] ?? null;
        if (is_array($data)) {
            /** @var array<string, mixed> $data */
            return $data;
        }
        return [];
    }

    /**
     * Decode the payload as a typed PaymentResponse — only valid for
     * payment_* event types. Returns null when the SDK can't construct
     * the typed DTO (missing required fields).
     */
    public function asPayment(): ?PaymentResponse
    {
        return $this->tryDecode(PaymentResponse::fromArray(...));
    }

    public function asRefund(): ?RefundResponse
    {
        return $this->tryDecode(RefundResponse::fromArray(...));
    }

    public function asDispute(): ?DisputeResponse
    {
        return $this->tryDecode(DisputeResponse::fromArray(...));
    }

    public function asMandate(): ?MandateResponse
    {
        return $this->tryDecode(MandateResponse::fromArray(...));
    }

    public function asPayout(): ?PayoutResponse
    {
        return $this->tryDecode(PayoutResponse::fromArray(...));
    }

    public function asSubscription(): ?SubscriptionResponse
    {
        return $this->tryDecode(SubscriptionResponse::fromArray(...));
    }

    public function asCustomer(): ?CustomerResponse
    {
        return $this->tryDecode(CustomerResponse::fromArray(...));
    }

    /**
     * @template T
     * @param callable(array<string, mixed>): T $builder
     * @return T|null
     */
    private function tryDecode(callable $builder): mixed
    {
        if ($this->payload === []) {
            return null;
        }
        try {
            return $builder($this->payload);
        } catch (\Throwable) {
            return null;
        }
    }
}
