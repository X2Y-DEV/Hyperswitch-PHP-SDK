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
     * Decode an object-shaped JSON response. The vast majority of Hyperswitch
     * endpoints return objects; this is the default path.
     *
     * @return array<string, mixed>
     *
     * @throws HyperswitchApiException when the JSON is malformed, non-array,
     *         or arrives as a JSON array instead of an object.
     */
    public static function decode(string $raw): array
    {
        $decoded = self::decodeAny($raw);
        if (array_is_list($decoded) && $decoded !== []) {
            throw new HyperswitchApiException(
                'Hyperswitch returned a JSON array where an object was expected.'
            );
        }
        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * Decode any JSON-array-or-object body. Use this for endpoints that may
     * return either shape (e.g. list endpoints — `/customers/list` returns a
     * bare array, `/refunds/list` returns an envelope object).
     *
     * @return array<int|string, mixed>
     */
    public static function decodeAny(string $raw): array
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
        return $decoded;
    }
}
