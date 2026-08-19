<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto;

/**
 * Base for every Response DTO.
 *
 * Subclasses implement `fromArray()` to construct themselves from a decoded JSON
 * payload. Unknown fields are preserved in `$extras` so non-breaking HyperSwitch
 * additions don't crash existing consumers — callers can inspect `extras()` to
 * read fields the SDK doesn't yet model.
 *
 * Round-tripping a response through fromArray() → toArray() is loss-less:
 * known typed fields plus untouched extras = original payload.
 */
abstract class AbstractResponseDto implements DtoInterface
{
    /** @var array<string, mixed> */
    protected array $extras;

    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(array $extras = [])
    {
        $this->extras = $extras;
    }

    /**
     * Construct a response DTO from a decoded JSON payload.
     *
     * @param array<string, mixed> $data
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Unknown / not-yet-modelled fields in the original response.
     *
     * @return array<string, mixed>
     */
    final public function extras(): array
    {
        return $this->extras;
    }

    /**
     * Lookup helper for fields not yet modelled.
     */
    final public function extra(string $key): mixed
    {
        return $this->extras[$key] ?? null;
    }

    /**
     * Decode an optional nested-object field into an associative array, or null
     * when absent / not an object. Used for spec shapes the SDK keeps loose
     * (`sdk_next_action`, `next_action`, connector blobs, feature metadata, …).
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    final protected static function objectField(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if (!is_array($value)) {
            return null;
        }
        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * Decode a JSON array of scalars into a list of strings (empty when absent).
     * Used for "available filter" responses (connectors, currencies, statuses …).
     *
     * @param array<string, mixed> $data
     * @return list<string>
     */
    final protected static function stringListField(array $data, string $key): array
    {
        $value = $data[$key] ?? null;
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            if (is_scalar($item)) {
                $out[] = (string) $item;
            }
        }
        return $out;
    }
}
