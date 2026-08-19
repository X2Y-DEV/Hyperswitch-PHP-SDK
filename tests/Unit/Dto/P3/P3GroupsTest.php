<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\P3;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Blocklist\Response\BlocklistResponse;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Request\CardIssuerRequest;
use X2Y\HyperSwitchClient\Dto\CardIssuer\Response\CardIssuerDeleteResponse;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmCreateRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmKeyRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Response\GsmDeleteResponse;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request\ProfileAcquirerCreateRequest;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Response\ProfileAcquirerResponse;
use X2Y\HyperSwitchClient\Endpoint\GsmEndpoints;

final class P3GroupsTest extends TestCase
{
    public function testBlocklistResponse(): void
    {
        $r = BlocklistResponse::fromArray([
            'fingerprint_id' => 'fp_1',
            'data_kind'      => 'card_bin',
            'created_at'     => '2026-01-01T00:00:00Z',
        ]);
        self::assertSame('fp_1', $r->fingerprintId);
        self::assertSame('card_bin', $r->dataKind);
        self::assertNotNull($r->createdAt);
    }

    public function testGsmCreateAndKey(): void
    {
        $create = new GsmCreateRequest(
            connector: 'stripe',
            flow: 'authorize',
            subFlow: 'payment',
            code: '00',
            message: 'ok',
            status: 'active',
            decision: 'proceed',
            stepUpPossible: false,
            clearPanPossible: true,
        );
        $arr = $create->toArray();
        self::assertSame('stripe', $arr['connector']);
        self::assertTrue($arr['clear_pan_possible']);

        $key = new GsmKeyRequest('stripe', 'authorize', 'payment', '00', 'ok');
        self::assertSame('payment', $key->toArray()['sub_flow']);

        self::assertSame(AuthTier::ADMIN, GsmEndpoints::create()->authTier);

        $del = GsmDeleteResponse::fromArray(['gsm_rule_delete' => true, 'connector' => 'stripe', 'flow' => 'authorize', 'sub_flow' => 'payment', 'code' => '00']);
        self::assertTrue($del->gsmRuleDelete);
    }

    public function testCardIssuer(): void
    {
        self::assertSame(['issuer_name' => 'Chase'], (new CardIssuerRequest(issuerName: 'Chase'))->toArray());
        $del = CardIssuerDeleteResponse::fromArray(['id' => 'ci_1', 'deleted' => true]);
        self::assertTrue($del->deleted);
    }

    public function testProfileAcquirer(): void
    {
        $req = new ProfileAcquirerCreateRequest(
            profileId: 'pro_1',
            merchantName: 'Acme',
            network: 'visa',
            acquirerBin: '411111',
            acquirerAssignedMerchantId: 'm_1',
            extras: ['is_default' => true],
        );
        $arr = $req->toArray();
        self::assertSame('pro_1', $arr['profile_id']);
        self::assertTrue($arr['is_default']);

        $resp = ProfileAcquirerResponse::fromArray([
            'profile_acquirer_id' => 'pa_1',
            'profile_id'          => 'pro_1',
            'is_default'          => false,
        ]);
        self::assertSame('pa_1', $resp->profileAcquirerId);
    }
}
