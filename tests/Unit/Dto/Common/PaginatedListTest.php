<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Dto\Common;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Dto\Common\PaginatedList;

final class PaginatedListTest extends TestCase
{
    public function testEnvelopeShape(): void
    {
        /** @var PaginatedList<array<string, mixed>> $list */
        $list = PaginatedList::fromArray(
            [
                'data' => [
                    ['id' => 'a'],
                    ['id' => 'b'],
                ],
                'size'        => 2,
                'total_count' => 50,
            ],
            static fn (array $row): array => $row,
        );
        self::assertCount(2, $list->items());
        self::assertSame(2, $list->size);
        self::assertSame(50, $list->totalCount);
        self::assertSame(2, $list->count());
    }

    public function testBareArrayShape(): void
    {
        /** @var PaginatedList<array<string, mixed>> $list */
        $list = PaginatedList::fromArray(
            [['id' => 'a'], ['id' => 'b'], ['id' => 'c']],
            static fn (array $row): array => $row,
        );
        self::assertCount(3, $list->items());
        self::assertNull($list->size);
        self::assertNull($list->totalCount);
    }

    public function testEmptyEnvelope(): void
    {
        /** @var PaginatedList<array<string, mixed>> $list */
        $list = PaginatedList::fromArray(
            ['data' => [], 'total_count' => 0],
            static fn (array $row): array => $row,
        );
        self::assertSame(0, $list->count());
        self::assertSame(0, $list->totalCount);
    }

    public function testEmptyArrayShape(): void
    {
        /** @var PaginatedList<array<string, mixed>> $list */
        $list = PaginatedList::fromArray([], static fn (array $row): array => $row);
        self::assertSame(0, $list->count());
        self::assertNull($list->size);
        self::assertNull($list->totalCount);
    }

    public function testItemBuilderIsCalledForEachRow(): void
    {
        $count = 0;
        /** @var PaginatedList<int> $list */
        $list = PaginatedList::fromArray(
            ['data' => [['x' => 1], ['x' => 2], ['x' => 3]]],
            function (array $row) use (&$count): int {
                $count++;
                return (int) $row['x'];
            },
        );
        self::assertSame(3, $count);
        self::assertSame([1, 2, 3], $list->items());
    }

    public function testEnvelopeExtrasPreserved(): void
    {
        /** @var PaginatedList<array<string, mixed>> $list */
        $list = PaginatedList::fromArray(
            [
                'data'        => [],
                'size'        => 0,
                'total_count' => 0,
                'next_cursor' => 'cursor_xyz',  // not modelled — should land in extras
            ],
            static fn (array $row): array => $row,
        );
        self::assertArrayHasKey('next_cursor', $list->extras);
        self::assertSame('cursor_xyz', $list->extras['next_cursor']);
    }

    public function testNonArrayRowsAreSkipped(): void
    {
        /** @var PaginatedList<array<string, mixed>> $list */
        $list = PaginatedList::fromArray(
            ['data' => [['id' => 'a'], 'not-an-array', ['id' => 'b']]],
            static fn (array $row): array => $row,
        );
        // Defensive: only the two valid array rows
        self::assertCount(2, $list->items());
    }
}
