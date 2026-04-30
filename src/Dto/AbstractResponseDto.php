<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto;

/**
 * Base for every Response DTO.
 *
 * Subclasses implement `fromArray()` to construct themselves from a decoded JSON
 * payload. Unknown fields are preserved in `$extras` so non-breaking Hyperswitch
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
}
