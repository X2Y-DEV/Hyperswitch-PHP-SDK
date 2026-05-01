<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Internal;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\Internal\Json;

final class JsonTest extends TestCase
{
    public function testEncodesAssociativeArray(): void
    {
        self::assertSame(
            '{"amount":100,"currency":"USD"}',
            Json::encode(['amount' => 100, 'currency' => 'USD'])
        );
    }

    public function testEncodeDoesNotEscapeSlashes(): void
    {
        self::assertSame('{"url":"https://example.com"}', Json::encode(['url' => 'https://example.com']));
    }

    public function testEncodeDoesNotEscapeUnicode(): void
    {
        self::assertSame('{"name":"José"}', Json::encode(['name' => 'José']));
    }

    public function testEncodeFailureSurfacesAsHyperSwitchException(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        $this->expectExceptionMessage('Failed to encode');
        // Resource handles aren't JSON-encodable
        $resource = fopen('php://memory', 'r');
        try {
            Json::encode(['handle' => $resource]);
        } finally {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }
    }

    public function testDecodeObjectShape(): void
    {
        self::assertSame(['payment_id' => 'pay_x'], Json::decode('{"payment_id":"pay_x"}'));
    }

    public function testDecodeEmptyStringReturnsEmptyArray(): void
    {
        self::assertSame([], Json::decode(''));
    }

    public function testDecodeRejectsBareJsonArrayWhenObjectExpected(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        $this->expectExceptionMessage('JSON array where an object was expected');
        Json::decode('[1, 2, 3]');
    }

    public function testDecodeRejectsScalarTopLevel(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        Json::decode('"just a string"');
    }

    public function testDecodeRejectsMalformedJson(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        $this->expectExceptionMessage('Failed to decode');
        Json::decode('{"oops":');
    }

    public function testDecodeAnyAcceptsBareArray(): void
    {
        self::assertSame([1, 2, 3], Json::decodeAny('[1,2,3]'));
    }

    public function testDecodeAnyAcceptsObject(): void
    {
        self::assertSame(['k' => 'v'], Json::decodeAny('{"k":"v"}'));
    }

    public function testDecodeAnyEmptyReturnsEmptyArray(): void
    {
        self::assertSame([], Json::decodeAny(''));
    }

    public function testDecodeAnyRejectsNonArrayJson(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        Json::decodeAny('42');
    }
}
