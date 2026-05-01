<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Common;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Common\Address;

final class AddressTest extends TestCase
{
    public function testToArrayDropsNullFields(): void
    {
        $a = new Address(line1: '1 Main St', city: 'SF', country: 'US');
        self::assertSame(
            ['line1' => '1 Main St', 'city' => 'SF', 'country' => 'US'],
            $a->toArray(),
        );
    }

    public function testToArrayWithAllFields(): void
    {
        $a = new Address(
            line1:     '1467',
            line2:     'Harrison Street',
            line3:     'Suite 4',
            city:      'San Francisco',
            state:     'California',
            zip:       '94122',
            country:   'US',
            firstName: 'Joseph',
            lastName:  'Doe',
        );
        $arr = $a->toArray();
        self::assertSame('Joseph', $arr['first_name']);    // camelCase → snake_case
        self::assertSame('Doe',    $arr['last_name']);
        self::assertCount(9, $arr);
    }

    public function testFromArrayHydratesFields(): void
    {
        $a = Address::fromArray([
            'line1'      => '1 Main',
            'city'       => 'SF',
            'first_name' => 'Joseph',
            'last_name'  => 'Doe',
        ]);
        self::assertSame('1 Main', $a->line1);
        self::assertSame('SF', $a->city);
        self::assertSame('Joseph', $a->firstName);
        self::assertSame('Doe', $a->lastName);
        self::assertNull($a->state);
    }

    public function testRoundTrip(): void
    {
        $original = [
            'line1'      => '1467',
            'city'       => 'SF',
            'state'      => 'CA',
            'zip'        => '94122',
            'country'    => 'US',
            'first_name' => 'Joseph',
            'last_name'  => 'Doe',
        ];
        self::assertSame($original, Address::fromArray($original)->toArray());
    }

    public function testEmptyArrayProducesAllNullDto(): void
    {
        $a = Address::fromArray([]);
        self::assertSame([], $a->toArray());
    }
}
