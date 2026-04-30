<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\ApiKey\Response;

use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Internal\DateTimeParser;

/**
 * Response from API key create / update / retrieve / list (item shape).
 *
 * `apiKey` is only present on the *create* response — once minted it is not
 * returned again. `prefix` (e.g. "snd_xxx", "live_xxx") is what later calls
 * surface for identification.
 *
 * @postman             API Key / Create API Key
 * @endpoint            POST /api_keys/{merchant_id}
 * @hyperswitch-source  2026-04-30
 */
final class ApiKeyResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $keyId,
        public readonly string $merchantId,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?string $apiKey = null,
        public readonly ?string $prefix = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
        public readonly ?\DateTimeImmutable $expiration = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['key_id', 'merchant_id', 'name', 'description', 'api_key', 'prefix', 'created_at', 'expiration'];

        return new self(
            keyId:       (string) $data['key_id'],
            merchantId:  (string) $data['merchant_id'],
            name:        (string) $data['name'],
            description: isset($data['description']) ? (string) $data['description'] : null,
            apiKey:      isset($data['api_key'])     ? (string) $data['api_key']     : null,
            prefix:      isset($data['prefix'])      ? (string) $data['prefix']      : null,
            createdAt:   DateTimeParser::parse($data['created_at'] ?? null),
            expiration:  DateTimeParser::parse($data['expiration'] ?? null),
            extras:      array_diff_key($data, array_flip($known)),
        );
    }
}
