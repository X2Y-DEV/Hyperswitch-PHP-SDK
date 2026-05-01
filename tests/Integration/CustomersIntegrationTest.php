<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Integration;

use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Customer\Request\CreateCustomerRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Request\CreateEphemeralKeyRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Request\UpdateCustomerRequest;
use X2Y\HyperSwitchClient\Exception\HyperSwitchExceptionInterface;
use X2Y\HyperSwitchClient\Exception\NotFoundException;

/**
 * Real-sandbox lifecycle for the Customers resource.
 */
final class CustomersIntegrationTest extends AbstractIntegrationTestCase
{
    public function testFullLifecycle(): void
    {
        $customerId = $this->uniqueId('cus');

        // 1. Create
        $created = $this->client->customers()->create(new CreateCustomerRequest(
            customerId: $customerId,
            email:      'integration@example.test',
            name:       'Integration Test',
            address:    new Address(line1: '1 Test St', city: 'SF', country: 'US'),
            metadata:   ['source' => 'sdk_integration'],
        ));
        $this->onTearDown(function () use ($customerId): void {
            try {
                $this->client->customers()->delete($customerId);
            } catch (HyperSwitchExceptionInterface) {
                // already deleted by the test path
            }
        });

        self::assertSame($customerId, $created->customerId);
        self::assertSame('Integration Test', $created->name);
        self::assertNotNull($created->createdAt);

        // 2. Retrieve
        $got = $this->client->customers()->retrieve($customerId);
        self::assertSame($customerId, $got->customerId);
        self::assertSame('integration@example.test', $got->email);
        self::assertNotNull($got->address);
        self::assertSame('SF', $got->address->city);

        // 3. Update — change name, leave the rest alone
        $updated = $this->client->customers()->update($customerId, new UpdateCustomerRequest(
            name: 'Integration Updated',
        ));
        self::assertSame('Integration Updated', $updated->name);
        self::assertSame('integration@example.test', $updated->email);

        // 4. Ephemeral key — exercises Unix-epoch DateTimeParser path
        $eph = $this->client->customers()->createEphemeralKey(new CreateEphemeralKeyRequest($customerId));
        self::assertNotEmpty($eph->id);
        self::assertNotEmpty($eph->secret);
        self::assertSame($customerId, $eph->customerId);
        self::assertNotNull($eph->createdAt, 'ephemeral key created_at should be parsed');
        self::assertNotNull($eph->expires, 'ephemeral key expires should be parsed');

        // 5. List — sandbox may have many customers; just confirm shape
        $list = $this->client->customers()->list();
        self::assertGreaterThanOrEqual(1, $list->count());

        // 6. Delete — HyperSwitch soft-deletes: the record remains retrievable
        // with `customer_deleted: true` rather than returning 404 afterward.
        $deleted = $this->client->customers()->delete($customerId);
        self::assertTrue($deleted->customerDeleted);

        $afterDelete = $this->client->customers()->retrieve($customerId);
        self::assertSame($customerId, $afterDelete->customerId);
    }

    public function testRetrieveNonExistentReturnsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->customers()->retrieve($this->uniqueId('cus_nope'));
    }
}
