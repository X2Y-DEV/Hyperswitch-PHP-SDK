<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\IntentStatus;

/**
 * @openapi             "Create Post Session Tokens for a Payment"
 * @endpoint            POST /payments/{id}/post_session_tokens
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class PostSessionTokensResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $nextAction
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $paymentId,
        public readonly IntentStatus $status,
        public readonly ?array $nextAction = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['payment_id', 'status', 'next_action'];

        return new self(
            paymentId:  (string) $data['payment_id'],
            status:     IntentStatus::from((string) $data['status']),
            nextAction: self::objectField($data, 'next_action'),
            extras:     array_diff_key($data, array_flip($known)),
        );
    }
}
