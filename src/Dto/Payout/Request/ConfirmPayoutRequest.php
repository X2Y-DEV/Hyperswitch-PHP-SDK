<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payout\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Confirm a previously-created payout by attaching the payout method.
 *
 * @endpoint            POST /payouts/{id}/confirm
 * @hyperswitch-source  2026-04-30
 */
final class ConfirmPayoutRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $payoutMethodData
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?array $payoutMethodData = null,
        public readonly ?string $clientSecret = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payout_method_data' => $this->payoutMethodData,
            'client_secret'      => $this->clientSecret,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
