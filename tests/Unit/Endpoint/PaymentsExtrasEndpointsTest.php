<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Endpoint;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Auth\AuthTier;
use X2Y\HyperSwitchClient\Endpoint\PaymentsEndpoints;

final class PaymentsExtrasEndpointsTest extends TestCase
{
    /**
     * @return array<string, array{0: callable(): \X2Y\HyperSwitchClient\Transport\Endpoint, 1: string, 2: string, 3: AuthTier}>
     */
    public static function endpointProvider(): array
    {
        return [
            'cancelPostCaptureRetrieve' => [PaymentsEndpoints::cancelPostCaptureRetrieve(...), 'GET', '/payments/{id}/cancel_post_capture', AuthTier::MERCHANT],
            'completeAuthorize'         => [PaymentsEndpoints::completeAuthorize(...), 'POST', '/payments/{id}/complete_authorize', AuthTier::PUBLISHABLE],
            'externalAuthentication'    => [PaymentsEndpoints::externalAuthentication(...), 'POST', '/payments/{id}/3ds/authentication', AuthTier::PUBLISHABLE],
            'eligibility'               => [PaymentsEndpoints::eligibility(...), 'POST', '/payments/{id}/eligibility', AuthTier::PUBLISHABLE],
            'eligibilityCheck'          => [PaymentsEndpoints::eligibilityCheck(...), 'POST', '/payments/{id}/eligibility_check', AuthTier::PUBLISHABLE],
            'postSessionTokens'         => [PaymentsEndpoints::postSessionTokens(...), 'POST', '/payments/{id}/post_session_tokens', AuthTier::PUBLISHABLE],
            'updateMetadata'            => [PaymentsEndpoints::updateMetadata(...), 'POST', '/payments/{id}/update_metadata', AuthTier::MERCHANT],
        ];
    }

    /**
     * @param callable(): \X2Y\HyperSwitchClient\Transport\Endpoint $factory
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('endpointProvider')]
    public function testEndpointShape(callable $factory, string $method, string $path, AuthTier $tier): void
    {
        $endpoint = $factory();
        self::assertSame($method, $endpoint->method);
        self::assertSame($path, $endpoint->pathTemplate);
        self::assertSame($tier, $endpoint->authTier);
    }
}
