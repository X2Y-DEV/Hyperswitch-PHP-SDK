<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto;

/**
 * Base for every Request DTO.
 *
 * Subclasses implement `toArray()` to produce the JSON body HyperSwitch expects.
 * The `filterNulls()` helper drops null fields so optional parameters omitted by
 * the caller don't end up as `null` in the request body.
 *
 * `mergeExtras()` lets DTOs with very large bodies expose an opt-in passthrough
 * for fields the SDK hasn't typed yet — symmetric with `extras` on response
 * DTOs. Typed fields take precedence over the same key in `$extras`.
 */
abstract class AbstractRequestDto implements DtoInterface
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    /**
     * Recursively filter out null values from a payload, including nested arrays.
     * Empty arrays after filtering are also dropped (so empty optional objects
     * don't serialise as `"foo": {}`).
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    final protected function filterNulls(array $payload): array
    {
        $filtered = [];
        foreach ($payload as $key => $value) {
            if ($value === null) {
                continue;
            }
            if (is_array($value)) {
                $cleaned = $this->filterNulls($value);
                if ($cleaned !== []) {
                    $filtered[$key] = $cleaned;
                }
                continue;
            }
            $filtered[$key] = $value;
        }
        return $filtered;
    }

    /**
     * Merge typed fields with caller-supplied extras. Typed fields win on key
     * collision so consumers can't accidentally mask a typed field with an
     * extras value.
     *
     * @param array<string, mixed> $typed
     * @param array<string, mixed> $extras
     * @return array<string, mixed>
     */
    final protected function mergeExtras(array $typed, array $extras): array
    {
        // PHP `+` keeps the LEFT operand's keys on collision; typed must come first.
        return $typed + $extras;
    }
}
