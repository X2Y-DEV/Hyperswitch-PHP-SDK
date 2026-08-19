<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Connector\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @endpoint            DELETE /account/{account_id}/connectors/{connector_id}
 * @hyperswitch-source  2026-04-30
 */
final class ConnectorDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $merchantId,
        public readonly string $merchantConnectorId,
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
        $known = ['merchant_id', 'merchant_connector_id', 'deleted'];

        return new self(
            merchantId:          (string) ($data['merchant_id'] ?? ''),
            merchantConnectorId: (string) ($data['merchant_connector_id'] ?? ''),
            deleted:             (bool)   ($data['deleted'] ?? false),
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
