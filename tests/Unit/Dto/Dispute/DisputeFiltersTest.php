<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Dispute;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Dto\Dispute\Request\DeleteEvidenceRequest;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\CreateFileResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputeListFiltersResponse;
use X2Y\HyperSwitchClient\Dto\Dispute\Response\DisputesAggregateResponse;

final class DisputeFiltersTest extends TestCase
{
    public function testDeleteEvidenceRequest(): void
    {
        $req = new DeleteEvidenceRequest(disputeId: 'dp_1', evidenceType: 'cancellation_policy');
        self::assertSame([
            'dispute_id'    => 'dp_1',
            'evidence_type' => 'cancellation_policy',
        ], $req->toArray());
    }

    public function testAggregateResponse(): void
    {
        $r = DisputesAggregateResponse::fromArray([
            'status_with_count' => ['dispute_opened' => 3, 'dispute_won' => 1],
        ]);
        self::assertSame(['dispute_opened' => 3, 'dispute_won' => 1], $r->statusWithCount);
    }

    public function testListFiltersResponse(): void
    {
        $r = DisputeListFiltersResponse::fromArray([
            'connector'      => ['stripe'],
            'currency'       => ['USD'],
            'dispute_stage'  => ['dispute'],
            'dispute_status' => ['dispute_opened'],
        ]);
        self::assertSame(['stripe'], $r->connector);
        self::assertSame(['USD'], $r->currency);
        self::assertSame(['dispute'], $r->disputeStage);
        self::assertSame(['dispute_opened'], $r->disputeStatus);
    }

    public function testCreateFileResponse(): void
    {
        $r = CreateFileResponse::fromArray(['file_id' => 'file_9']);
        self::assertSame('file_9', $r->fileId);
    }
}
