<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Integration;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Refund\Request\ListRefundsRequest;
use X2Y\HyperswitchClient\Dto\Refund\Response\RefundResponse;
use X2Y\HyperswitchClient\Exception\NotFoundException;

final class RefundsIntegrationTest extends AbstractIntegrationTestCase
{
    public function testRetrieveNonExistentReturnsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->refunds()->retrieve($this->uniqueId('ref'));
    }

    public function testListReturnsTypedPaginatedList(): void
    {
        $list = $this->client->refunds()->list(new ListRefundsRequest(limit: 5));
        self::assertInstanceOf(PaginatedList::class, $list);

        foreach ($list->items() as $refund) {
            self::assertInstanceOf(RefundResponse::class, $refund);
        }
    }
}
