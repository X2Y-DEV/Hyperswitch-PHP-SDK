<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Internal;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\Internal\DateTimeParser;

final class DateTimeParserTest extends TestCase
{
    public function testNullReturnsNull(): void
    {
        self::assertNull(DateTimeParser::parse(null));
    }

    public function testEmptyStringReturnsNull(): void
    {
        self::assertNull(DateTimeParser::parse(''));
    }

    public function testIsoStringIsParsed(): void
    {
        $dt = DateTimeParser::parse('2026-04-30T12:34:56Z');
        self::assertNotNull($dt);
        self::assertSame('2026-04-30T12:34:56+00:00', $dt->format('c'));
    }

    public function testIsoWithMillisIsParsed(): void
    {
        $dt = DateTimeParser::parse('2026-04-30T12:34:56.789Z');
        self::assertNotNull($dt);
        self::assertSame(2026, (int) $dt->format('Y'));
    }

    public function testIntegerEpochIsParsedAsUtc(): void
    {
        $dt = DateTimeParser::parse(1777581234);
        self::assertNotNull($dt);
        self::assertSame('UTC', $dt->getTimezone()->getName());
        self::assertSame(1777581234, $dt->getTimestamp());
    }

    public function testNumericStringEpochIsParsedAsUtc(): void
    {
        $dt = DateTimeParser::parse('1777581234');
        self::assertNotNull($dt);
        self::assertSame(1777581234, $dt->getTimestamp());
    }

    public function testInvalidDatetimeStringThrows(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        $this->expectExceptionMessage('Failed to parse datetime');
        DateTimeParser::parse('not-a-date');
    }

    public function testUnsupportedTypeThrows(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        $this->expectExceptionMessage('Unexpected datetime value type');
        DateTimeParser::parse(['not', 'a', 'date']);
    }

    public function testFloatIsRejectedAsUnexpectedType(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        DateTimeParser::parse(1.5);
    }
}
