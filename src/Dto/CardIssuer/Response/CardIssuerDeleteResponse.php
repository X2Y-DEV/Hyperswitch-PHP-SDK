<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\CardIssuer\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @openapi             "Delete Card Issuer"
 * @endpoint            DELETE /card_issuers/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class CardIssuerDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $id,
        public readonly bool $deleted,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id:      (string) $data['id'],
            deleted: (bool) ($data['deleted'] ?? false),
            extras:  array_diff_key($data, array_flip(['id', 'deleted'])),
        );
    }
}
