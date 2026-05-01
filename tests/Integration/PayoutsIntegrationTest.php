<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Integration;

use X2Y\HyperSwitchClient\Exception\NotFoundException;

final class PayoutsIntegrationTest extends AbstractIntegrationTestCase
{
    public function testRetrieveNonExistentReturnsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->payouts()->retrieve($this->uniqueId('po'));
    }
}
