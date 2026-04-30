<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Relay\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Enum\RelayType;
use X2Y\HyperswitchClient\Internal\DateTimeParser;

/**
 * Response from /relay create / retrieve.
 *
 * @postman             Relay / Relay - Create
 * @endpoint            POST /relay
 * @hyperswitch-source  2026-04-30
 */
final class RelayResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $data
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly string $id,
        public readonly ?RelayType $type = null,
        public readonly ?string $status = null,
        public readonly ?string $connectorId = null,
        public readonly ?string $connectorResourceId = null,
        public readonly ?string $connectorReferenceId = null,
        public readonly ?array $data = null,
        public readonly ?string $errorCode = null,
        public readonly ?string $errorMessage = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
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
            'id', 'relay_id', 'type', 'status',
            'connector_id', 'connector_resource_id', 'connector_reference_id',
            'data', 'error_code', 'error_message', 'created_at',
        ];

        $payload = $data['data'] ?? null;

        return new self(
            id:                   (string) ($data['id'] ?? $data['relay_id'] ?? ''),
            type:                 isset($data['type'])
                ? RelayType::from((string) $data['type'])
                : null,
            status:               isset($data['status'])                 ? (string) $data['status']                 : null,
            connectorId:          isset($data['connector_id'])           ? (string) $data['connector_id']           : null,
            connectorResourceId:  isset($data['connector_resource_id'])  ? (string) $data['connector_resource_id']  : null,
            connectorReferenceId: isset($data['connector_reference_id']) ? (string) $data['connector_reference_id'] : null,
            data:                 is_array($payload) ? $payload : null,
            errorCode:            isset($data['error_code'])    ? (string) $data['error_code']    : null,
            errorMessage:         isset($data['error_message']) ? (string) $data['error_message'] : null,
            createdAt:            DateTimeParser::parse($data['created_at'] ?? null),
            extras:               array_diff_key($data, array_flip($known)),
        );
    }
}
