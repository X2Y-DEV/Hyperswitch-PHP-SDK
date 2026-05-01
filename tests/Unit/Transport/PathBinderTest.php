<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Transport;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\Transport\PathBinder;

final class PathBinderTest extends TestCase
{
    public function testSubstitutesSinglePlaceholder(): void
    {
        self::assertSame(
            '/payments/pay_abc/confirm',
            PathBinder::bind('/payments/{id}/confirm', ['id' => 'pay_abc']),
        );
    }

    public function testUrlEncodesPathValues(): void
    {
        self::assertSame(
            '/customers/cus%20with%20space',
            PathBinder::bind('/customers/{id}', ['id' => 'cus with space']),
        );
    }

    public function testAppendsQueryString(): void
    {
        $bound = PathBinder::bind(
            '/payments/list',
            [],
            ['limit' => 50, 'offset' => 0],
        );
        self::assertSame('/payments/list?limit=50&offset=0', $bound);
    }

    public function testThrowsOnMissingPathParam(): void
    {
        $this->expectException(HyperSwitchApiException::class);
        $this->expectExceptionMessage('Missing path parameter "id"');
        PathBinder::bind('/payments/{id}', []);
    }

    public function testHandlesTemplatesWithoutPlaceholders(): void
    {
        self::assertSame('/refunds', PathBinder::bind('/refunds'));
    }
}
