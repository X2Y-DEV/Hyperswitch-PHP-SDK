<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\PaymentLink\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Internal\DateTimeParser;

/**
 * Response from GET /payment_link/{id}.
 *
 * @postman             Payment Links / Payment Link - Retrieve
 * @endpoint            GET /payment_link/{id}
 * @hyperswitch-source  2026-04-30
 */
final class PaymentLinkResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $paymentLinkId,
        public readonly string $paymentId,
        public readonly string $merchantId,
        public readonly ?string $linkToPay = null,
        public readonly ?int $amount = null,
        public readonly ?Currency $currency = null,
        public readonly ?string $description = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $linkExpiry = null,
        public readonly ?string $profileId = null,
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
            'payment_link_id', 'payment_id', 'merchant_id', 'link_to_pay',
            'amount', 'currency', 'description', 'created_at', 'link_expiry',
            'profile_id', 'return_url',
        ];

        return new self(
            paymentLinkId: (string) $data['payment_link_id'],
            paymentId:     (string) $data['payment_id'],
            merchantId:    (string) $data['merchant_id'],
            linkToPay:     isset($data['link_to_pay']) ? (string) $data['link_to_pay'] : null,
            amount:        isset($data['amount'])      ? (int)    $data['amount']      : null,
            currency:      isset($data['currency'])    ? Currency::from((string) $data['currency']) : null,
            description:   isset($data['description']) ? (string) $data['description'] : null,
            createdAt:     DateTimeParser::parse($data['created_at']  ?? null),
            linkExpiry:    DateTimeParser::parse($data['link_expiry'] ?? null),
            profileId:     isset($data['profile_id']) ? (string) $data['profile_id'] : null,
            returnUrl:     isset($data['return_url']) ? (string) $data['return_url'] : null,
            extras:        array_diff_key($data, array_flip($known)),
        );
    }
}
