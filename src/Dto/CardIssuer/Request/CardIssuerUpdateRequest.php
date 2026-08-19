<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\CardIssuer\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Update Card Issuer"
 * @endpoint            PUT /card_issuers/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CardIssuerUpdateRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $issuerName = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls(['issuer_name' => $this->issuerName]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
