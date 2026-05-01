<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ApiKey\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @postman             API Key / Delete API Key
 * @endpoint            DELETE /api_keys/{merchant_id}/{api_key_id}
 * @hyperswitch-source  2026-04-30
 */
final class ApiKeyDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $merchantId,
        public readonly string $keyIdDeleted,
        public readonly bool $revoked,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['merchant_id', 'key_id_deleted', 'revoked'];

        return new self(
            merchantId:   (string) $data['merchant_id'],
            keyIdDeleted: (string) ($data['key_id_deleted'] ?? $data['key_id'] ?? ''),
            revoked:      (bool)   ($data['revoked'] ?? false),
            extras:       array_diff_key($data, array_flip($known)),
        );
    }
}
