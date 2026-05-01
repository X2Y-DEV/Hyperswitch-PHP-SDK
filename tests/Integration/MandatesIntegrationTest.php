<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Integration;

use X2Y\HyperswitchClient\Exception\NotFoundException;

final class MandatesIntegrationTest extends AbstractIntegrationTestCase
{
    public function testRetrieveNonExistentReturnsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->mandates()->retrieve($this->uniqueId('man'));
    }

    public function testListForNonExistentCustomerReturnsNotFound(): void
    {
        // Hyperswitch returns 404 (not an empty list) when no mandates exist
        // for a customer — we verified this manually during Phase 3.
        $this->expectException(NotFoundException::class);
        $this->client->mandates()->listForCustomer($this->uniqueId('cus_nope'));
    }
}
