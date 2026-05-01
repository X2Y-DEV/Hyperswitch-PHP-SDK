<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

final class AbstractRequestDtoTest extends TestCase
{
    private function dto(): AbstractRequestDto
    {
        return new class extends AbstractRequestDto {
            public function toArray(): array
            {
                return [];
            }

            /** @param array<string, mixed> $payload */
            public function callFilterNulls(array $payload): array
            {
                return $this->filterNulls($payload);
            }

            /**
             * @param array<string, mixed> $typed
             * @param array<string, mixed> $extras
             */
            public function callMergeExtras(array $typed, array $extras): array
            {
                return $this->mergeExtras($typed, $extras);
            }
        };
    }

    public function testFilterNullsDropsTopLevelNulls(): void
    {
        self::assertSame(
            ['a' => 1, 'c' => 'x'],
            $this->dto()->callFilterNulls(['a' => 1, 'b' => null, 'c' => 'x']),
        );
    }

    public function testFilterNullsRecursesIntoNestedArrays(): void
    {
        self::assertSame(
            ['outer' => ['inner' => 'kept']],
            $this->dto()->callFilterNulls([
                'outer' => ['inner' => 'kept', 'dropped' => null],
            ]),
        );
    }

    public function testFilterNullsDropsEmptyNestedArrays(): void
    {
        self::assertSame(
            ['kept' => 1],
            $this->dto()->callFilterNulls([
                'kept' => 1,
                'allnull' => ['a' => null, 'b' => null],
            ]),
        );
    }

    public function testFilterNullsKeepsZeroAndFalse(): void
    {
        self::assertSame(
            ['zero' => 0, 'flag' => false, 'empty_string' => ''],
            $this->dto()->callFilterNulls(['zero' => 0, 'flag' => false, 'empty_string' => '', 'nope' => null]),
        );
    }

    public function testMergeExtrasTypedFieldsWinOnCollision(): void
    {
        $merged = $this->dto()->callMergeExtras(
            typed: ['amount' => 100, 'currency' => 'USD'],
            extras: ['amount' => 999, 'extra_field' => 'kept'],
        );
        self::assertSame(100, $merged['amount']);                  // typed wins
        self::assertSame('USD', $merged['currency']);
        self::assertSame('kept', $merged['extra_field']);          // extras-only key passes through
    }

    public function testMergeExtrasEmptyExtrasReturnsTypedOnly(): void
    {
        self::assertSame(
            ['a' => 1],
            $this->dto()->callMergeExtras(['a' => 1], []),
        );
    }

    public function testMergeExtrasEmptyTypedReturnsExtrasOnly(): void
    {
        self::assertSame(
            ['x' => 'y'],
            $this->dto()->callMergeExtras([], ['x' => 'y']),
        );
    }
}
