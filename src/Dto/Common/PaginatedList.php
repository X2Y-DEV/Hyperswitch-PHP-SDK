<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Common;

/**
 * Generic envelope for Hyperswitch list endpoints.
 *
 * Hyperswitch wraps list responses in `{"data": [...], "size": N}` (sometimes
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
     * @template U
     *
     * @param array<string, mixed> $raw                 the decoded list envelope
     * @param callable(array<string, mixed>): U $itemBuilder constructs one item from one row
     *
     * @return self<U>
     */
    public static function fromArray(array $raw, callable $itemBuilder): self
    {
        $rows = $raw['data'] ?? [];
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
