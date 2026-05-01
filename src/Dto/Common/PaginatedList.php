<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Common;

/**
 * Generic envelope for HyperSwitch list endpoints.
 *
 * HyperSwitch wraps list responses in `{"data": [...], "size": N}` (sometimes
 * with `total_count` instead of or in addition to `size`). This class is the
 * uniform return type for every list-style Resource method, parameterised by
 * the element DTO.
 *
 * Resources construct it via `PaginatedList::fromArray($raw, $itemBuilder)`,
 * passing the decoded list envelope and a callable that knows how to build
 * one item DTO from one decoded row.
 *
 * @template T
 *
 * @postman             Common / PaginatedList
 * @hyperswitch-source  2026-04-30
 */
final class PaginatedList
{
    /**
     * @param list<T> $data
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly array $data,
        public readonly ?int $size = null,
        public readonly ?int $totalCount = null,
        public readonly array $extras = [],
    ) {
    }

    /**
     * Construct a paginated list from a HyperSwitch list response.
     *
     * HyperSwitch is inconsistent across endpoints:
     *   - Some return an envelope: `{"data": [...], "size": N, "total_count": N}`
     *     (e.g. `/refunds/list`)
     *   - Some return a bare array: `[ {...}, {...} ]` (e.g. `/customers/list`)
     *
     * Both shapes are accepted. For bare-array responses, `size` and
     * `totalCount` are null and `extras` is empty.
     *
     * @template U
     *
     * @param array<int|string, mixed> $raw                  decoded JSON body — list or envelope
     * @param callable(array<string, mixed>): U $itemBuilder constructs one item from one row
     *
     * @return self<U>
     */
    public static function fromArray(array $raw, callable $itemBuilder): self
    {
        // Bare-array response: items at the top level, no envelope metadata.
        if ($raw !== [] && array_is_list($raw)) {
            $items = [];
            foreach ($raw as $row) {
                if (is_array($row)) {
                    /** @var array<string, mixed> $row */
                    $items[] = $itemBuilder($row);
                }
            }
            return new self(data: $items);
        }

        // Envelope response.
        /** @var array<string, mixed> $raw */
        $rows  = $raw['data'] ?? [];
        $items = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (is_array($row)) {
                    /** @var array<string, mixed> $row */
                    $items[] = $itemBuilder($row);
                }
            }
        }

        $known = ['data', 'size', 'total_count'];

        return new self(
            data:       $items,
            size:       isset($raw['size'])        ? (int) $raw['size']        : null,
            totalCount: isset($raw['total_count']) ? (int) $raw['total_count'] : null,
            extras:     array_diff_key($raw, array_flip($known)),
        );
    }

    /**
     * Convenience: number of items returned in this page.
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return list<T>
     */
    public function items(): array
    {
        return $this->data;
    }
}
