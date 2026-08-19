<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Common;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Common\BillingDetails;

final class BillingDetailsTest extends TestCase
{
    public function testEmptyToArrayIsEmpty(): void
    {
        self::assertSame([], (new BillingDetails())->toArray());
    }

    public function testTopLevelFields(): void
    {
        $b = new BillingDetails(
            email: 'a@b.com',
            phone: '5551234567',
            phoneCountryCode: '+1',
        );
        // v1 spec nests the number + country_code inside a PhoneDetails object.
        self::assertSame(
            [
                'email' => 'a@b.com',
                'phone' => [
                    'number'       => '5551234567',
                    'country_code' => '+1',
                ],
            ],
            $b->toArray(),
        );
    }

    public function testNestedAddressIsSerialized(): void
    {
        $b = new BillingDetails(
            address: new Address(line1: '1 Main', city: 'SF', country: 'US'),
            email:   'a@b.com',
        );
        $arr = $b->toArray();
        self::assertSame(['line1' => '1 Main', 'city' => 'SF', 'country' => 'US'], $arr['address']);
        self::assertSame('a@b.com', $arr['email']);
    }
}
