<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

final class AbstractResponseDtoTest extends TestCase
{
    public function testExtrasDefaultsToEmpty(): void
    {
        $r = new class extends AbstractResponseDto {
            public static function fromArray(array $data): static
            {
                return new self();
            }
        };
        self::assertSame([], $r->extras());
    }

    public function testExtrasAccessor(): void
    {
        $r = new class(['unknown_field' => 'kept', 'another' => 42]) extends AbstractResponseDto {
            public static function fromArray(array $data): static
            {
                return new self($data);
            }
        };
        self::assertSame('kept', $r->extra('unknown_field'));
        self::assertSame(42, $r->extra('another'));
    }

    public function testExtraReturnsNullForMissingKey(): void
    {
        $r = new class([]) extends AbstractResponseDto {
            public static function fromArray(array $data): static
            {
                return new self($data);
            }
        };
        self::assertNull($r->extra('missing'));
    }

    public function testExtrasReturnsFullMap(): void
    {
        $r = new class(['a' => 1, 'b' => 2]) extends AbstractResponseDto {
            public static function fromArray(array $data): static
            {
                return new self($data);
            }
        };
        self::assertSame(['a' => 1, 'b' => 2], $r->extras());
    }
}
