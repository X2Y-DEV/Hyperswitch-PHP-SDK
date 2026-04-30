<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Relay\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Enum\RelayType;

/**
 * Relay an operation (capture / refund / void / incremental auth) directly
 * to a connector resource — used for cross-connector or out-of-band ops.
 *
 * `data` is the type-specific operation payload — keyed by the relay type
 * (`capture`, `void`, `incremental_authorization`, `refund`). Per the
 * connector-blob convention, kept as a loose `array<string, mixed>`.
 *
 * @postman             Relay / Relay - Create
 * @endpoint            POST /relay
 * @hyperswitch-source  2026-04-30
 */
final class CreateRelayRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $data    e.g. ['capture' => ['authorized_amount' => 1000, …]]
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $connectorId,
        public readonly string $connectorResourceId,
        public readonly RelayType $type,
        public readonly array $data,
        public readonly ?string $connectorReferenceId = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'connector_id'          => $this->connectorId,
            'connector_resource_id' => $this->connectorResourceId,
            'type'                  => $this->type->value,
            'data'                  => $this->data,
            'connector_reference_id'=> $this->connectorReferenceId,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
