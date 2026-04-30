<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto;

/**
 * Base for every Request DTO.
 *
 * Subclasses implement `toArray()` to produce the JSON body Hyperswitch expects.
 * The `filterNulls()` helper drops null fields so optional parameters omitted by
 * the caller don't end up as `null` in the request body.
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
}
