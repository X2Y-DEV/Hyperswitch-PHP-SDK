<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Payment\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\CaptureMethod;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\IntentStatus;

/**
 * Response from /payments create / retrieve / confirm / capture / cancel.
 *
 * Stub set of fields — the full Postman response has many more (mandate, attempts,
 * captures, customer, billing/shipping, etc.). AI maintenance extends this; the
 * structure here is the template. Unknown fields are preserved in `extras()`.
 *
 * @postman             Payments / Payments - Retrieve
 * @endpoint            GET /payments/{id}
 * @hyperswitch-source  2026-04-30
 */
final class PaymentResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly IntentStatus $status,
        public readonly int $amount,
        public readonly Currency $currency,
        public readonly ?string $clientSecret = null,
        public readonly ?string $customerId = null,
        public readonly ?int $amountCapturable = null,
        public readonly ?int $amountReceived = null,
        public readonly ?CaptureMethod $captureMethod = null,
        public readonly ?\DateTimeImmutable $created = null,
        public readonly ?string $description = null,
        public readonly ?string $returnUrl = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'payment_id', 'status', 'amount', 'currency', 'client_secret', 'customer_id',
            'amount_capturable', 'amount_received', 'capture_method', 'created',
            'description', 'return_url',
        ];

        return new self(
            paymentId:        (string) $data['payment_id'],
            status:           IntentStatus::from((string) $data['status']),
            amount:           (int) $data['amount'],
            currency:         Currency::from((string) $data['currency']),
            clientSecret:     isset($data['client_secret'])     ? (string) $data['client_secret']    : null,
            customerId:       isset($data['customer_id'])       ? (string) $data['customer_id']      : null,
            amountCapturable: isset($data['amount_capturable']) ? (int)    $data['amount_capturable']: null,
            amountReceived:   isset($data['amount_received'])   ? (int)    $data['amount_received']  : null,
            captureMethod:    isset($data['capture_method'])    ? CaptureMethod::from((string) $data['capture_method']) : null,
            created:          isset($data['created'])           ? new \DateTimeImmutable((string) $data['created']) : null,
            description:      isset($data['description'])       ? (string) $data['description']      : null,
            returnUrl:        isset($data['return_url'])        ? (string) $data['return_url']       : null,
            extras:           array_diff_key($data, array_flip($known)),
        );
    }
}
