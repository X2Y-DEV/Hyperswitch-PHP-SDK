<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Connector\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Result of registering a connector webhook. The connector-specific detail
 * fields (results, secret status, error codes) are preserved in `extras`.
 *
 * @openapi             "Register a Connector Webhook"
 * @endpoint            POST /account/{id}/webhooks/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class RegisterConnectorWebhookResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $connectorWebhookId = null,
        public readonly ?string $webhookRegistrationStatus = null,
        public readonly ?string $eventType = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
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
            'connector_webhook_id', 'webhook_registration_status', 'event_type',
            'error_code', 'error_message',
        ];

        return new self(
            connectorWebhookId:        isset($data['connector_webhook_id']) ? (string) $data['connector_webhook_id'] : null,
            webhookRegistrationStatus: isset($data['webhook_registration_status']) ? (string) $data['webhook_registration_status'] : null,
            eventType:                 isset($data['event_type']) ? (string) $data['event_type'] : null,
            errorCode:                 isset($data['error_code']) ? (string) $data['error_code'] : null,
            errorMessage:              isset($data['error_message']) ? (string) $data['error_message'] : null,
            extras:                    array_diff_key($data, array_flip($known)),
        );
    }
}
