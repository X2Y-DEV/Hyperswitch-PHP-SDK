<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Integration;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Dispute\Response\DisputeResponse;
use X2Y\HyperswitchClient\Exception\NotFoundException;

final class DisputesIntegrationTest extends AbstractIntegrationTestCase
{
    public function testListReturnsTypedPaginatedList(): void
    {
        $list = $this->client->disputes()->list();
        self::assertInstanceOf(PaginatedList::class, $list);
        foreach ($list->items() as $dispute) {
            self::assertInstanceOf(DisputeResponse::class, $dispute);
        }
    }

    public function testRetrieveNonExistentReturnsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->disputes()->retrieve($this->uniqueId('dp'));
    }
}
