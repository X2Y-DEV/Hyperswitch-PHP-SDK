<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Connector\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @openapi             "Register a Connector Webhook"
 * @endpoint            POST /account/{id}/webhooks/{id}
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ConnectorWebhookRegisterRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly ?string $eventType = null,
        public readonly ?string $scope = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'event_type' => $this->eventType,
            'scope'      => $this->scope,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
