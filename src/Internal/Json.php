<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Internal;

use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * Internal JSON helper. Wraps json_encode/json_decode with JSON_THROW_ON_ERROR
 * and surfaces failures as HyperswitchApiException.
 *
 * Not part of the public API — `@internal`.
 */
final class Json
{
    private function __construct()
    {
    }

    /**
     * @param mixed $value
     */
    public static function encode(mixed $value): string
    {
        try {
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
            throw new HyperswitchApiException('Failed to encode request body as JSON: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function decode(string $raw): array
    {
        if ($raw === '') {
            return [];
        }
        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new HyperswitchApiException('Failed to decode response body as JSON: ' . $e->getMessage(), 0, $e);
        }
        if (!is_array($decoded)) {
            throw new HyperswitchApiException('Hyperswitch returned a non-object JSON payload.');
        }
        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
