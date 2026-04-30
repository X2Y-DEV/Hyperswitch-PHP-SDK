<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Connector\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;

/**
 * Response from connector create / retrieve / update / list (item).
 *
 * @postman             PaymentConnectors / Payment Connector - Retrieve
 * @endpoint            GET /account/{account_id}/connectors/{connector_id}
 * @hyperswitch-source  2026-04-30
 */
final class ConnectorResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $merchantConnectorId,
        public readonly string $connectorName,
        public readonly ?string $connectorType = null,
        public readonly ?string $connectorLabel = null,
        public readonly ?string $profileId = null,
        public readonly ?bool $disabled = null,
        public readonly ?bool $testMode = null,
        public readonly ?string $status = null,
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
            'merchant_connector_id', 'connector_name', 'connector_type',
            'connector_label', 'profile_id', 'disabled', 'test_mode', 'status',
        ];

        return new self(
            merchantConnectorId: (string) $data['merchant_connector_id'],
            connectorName:       (string) $data['connector_name'],
            connectorType:       isset($data['connector_type'])  ? (string) $data['connector_type']  : null,
            connectorLabel:      isset($data['connector_label']) ? (string) $data['connector_label'] : null,
            profileId:           isset($data['profile_id'])      ? (string) $data['profile_id']      : null,
            disabled:            isset($data['disabled'])        ? (bool)   $data['disabled']        : null,
            testMode:            isset($data['test_mode'])       ? (bool)   $data['test_mode']       : null,
            status:              isset($data['status'])          ? (string) $data['status']          : null,
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
