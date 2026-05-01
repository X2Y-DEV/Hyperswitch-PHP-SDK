<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Internal;

use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Robust datetime parser for response fields.
 *
 * HyperSwitch is not consistent across endpoints: most timestamps come back
 * as ISO 8601 strings (`"2026-04-30T12:34:56.789Z"`), but some endpoints —
 * notably `POST /ephemeral_keys` — return Unix timestamps as integers
 * (`1777579037`). This helper accepts either.
 *
 * Use `DateTimeParser::parse()` for every datetime field in a Response DTO.
 * Do not construct `DateTimeImmutable` directly from response data.
 *
 * Not part of the public API — `@internal`.
 */
final class DateTimeParser
{
    private function __construct()
    {
    }

    /**
     * Parse a JSON-decoded value into a DateTimeImmutable.
     *
     * Accepted forms:
     *   - null / "" / missing             → null
     *   - int / numeric string            → treated as Unix epoch seconds
     *   - ISO 8601 string                 → parsed by DateTimeImmutable
     *
     * @throws HyperSwitchApiException when a non-empty value cannot be parsed.
     */
    public static function parse(mixed $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return (new \DateTimeImmutable('@' . $value))->setTimezone(new \DateTimeZone('UTC'));
        }

        if (is_string($value)) {
            // Unix timestamp arriving as a string ("1777579037")
            if (ctype_digit($value)) {
                return (new \DateTimeImmutable('@' . $value))->setTimezone(new \DateTimeZone('UTC'));
            }
            try {
                return new \DateTimeImmutable($value);
            } catch (\DateMalformedStringException | \Exception $e) {
                throw new HyperSwitchApiException(
                    'Failed to parse datetime value "' . $value . '": ' . $e->getMessage(),
                    0,
                    $e,
                );
            }
        }

        throw new HyperSwitchApiException(
            'Unexpected datetime value type: ' . get_debug_type($value)
        );
    }
}
