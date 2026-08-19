<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\CardIssuer\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Create Card Issuer"
 * @endpoint            POST /card_issuers
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CardIssuerRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $issuerName,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        return $this->mergeExtras(['issuer_name' => $this->issuerName], $this->extras);
    }
}
