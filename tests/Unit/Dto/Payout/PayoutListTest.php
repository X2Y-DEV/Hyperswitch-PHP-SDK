<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Payout;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Payout\Request\FilterPayoutsRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\ListPayoutsRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\PayoutFiltersRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Response\PayoutListFiltersResponse;
use X2Y\HyperSwitchClient\Enum\Currency;

final class PayoutListTest extends TestCase
{
    public function testListPayoutsQuery(): void
    {
        $req = new ListPayoutsRequest(customerId: 'cus_1', limit: 20);
        self::assertSame(['customer_id' => 'cus_1', 'limit' => 20], $req->toArray());
    }

    public function testFilterPayoutsRequiredAndOptional(): void
    {
        $req = new FilterPayoutsRequest(
            currency: Currency::USD,
            entityType: 'Individual',
            startTime: new \DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            connector: ['wise', 'adyen'],
        );
        $arr = $req->toArray();
        self::assertSame('USD', $arr['currency']);
        self::assertSame('Individual', $arr['entity_type']);
        self::assertSame('2026-01-01T00:00:00+00:00', $arr['start_time']);
        self::assertSame(['wise', 'adyen'], $arr['connector']);
    }

    public function testPayoutFiltersRequestSerialisesStartTime(): void
    {
        $req = new PayoutFiltersRequest(startTime: new \DateTimeImmutable('2026-02-02T03:04:05+00:00'));
        self::assertSame(['start_time' => '2026-02-02T03:04:05+00:00'], $req->toArray());
    }

    public function testPayoutListFiltersResponse(): void
    {
        $r = PayoutListFiltersResponse::fromArray([
            'connector'     => ['wise', 'adyen'],
            'currency'      => ['USD', 'EUR'],
            'status'        => ['success', 'failed'],
            'payout_method' => ['bank', 'card'],
        ]);
        self::assertSame(['wise', 'adyen'], $r->connector);
        self::assertSame(['USD', 'EUR'], $r->currency);
        self::assertSame(['success', 'failed'], $r->status);
        self::assertSame(['bank', 'card'], $r->payoutMethod);
    }
}
