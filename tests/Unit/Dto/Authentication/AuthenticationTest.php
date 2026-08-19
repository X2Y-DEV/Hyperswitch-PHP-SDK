<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Dto\Authentication;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationActionRequest;
use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationCreateRequest;
use X2Y\HyperSwitchClient\Dto\Authentication\Response\AuthenticationResponse;
use X2Y\HyperSwitchClient\Endpoint\AuthenticationEndpoints;

final class AuthenticationTest extends TestCase
{
    public function testCreateRequest(): void
    {
        $req = new AuthenticationCreateRequest(profileId: 'pro_1', authenticationConnector: 'threedsecureio');
        self::assertSame([
            'profile_id'               => 'pro_1',
            'authentication_connector' => 'threedsecureio',
        ], $req->toArray());
    }

    public function testActionRequestPassthrough(): void
    {
        $req = new AuthenticationActionRequest(clientSecret: 'cs', extras: ['device_channel' => 'BRW']);
        self::assertSame(['client_secret' => 'cs', 'device_channel' => 'BRW'], $req->toArray());
    }

    public function testResponseTolerant(): void
    {
        $r = AuthenticationResponse::fromArray([
            'authentication_id' => 'auth_1',
            'status'            => 'pending',
            'acs_url'           => 'https://acs',
        ]);
        self::assertSame('auth_1', $r->authenticationId);
        self::assertSame('pending', $r->status);
        self::assertSame('https://acs', $r->extra('acs_url'));
    }

    public function testRedirectIsPublishableAndEligibilityCheckIsGet(): void
    {
        self::assertSame(AuthTier::PUBLISHABLE, AuthenticationEndpoints::redirect()->authTier);
        self::assertSame('GET', AuthenticationEndpoints::eligibilityCheckRetrieve()->method);
    }
}
