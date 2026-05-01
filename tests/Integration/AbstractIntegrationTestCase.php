<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Integration;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;
use X2Y\HyperSwitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperSwitchClient\Auth\MerchantKeyV1AuthResolver;
use X2Y\HyperSwitchClient\HyperSwitchClient;
use X2Y\HyperSwitchClient\Transport\Middleware\IdempotencyKeyMiddleware;
use X2Y\HyperSwitchClient\Transport\Middleware\RetryMiddleware;

/**
 * Base class for tests that hit the real HyperSwitch sandbox.
 *
 * Skips cleanly when `HYPERSWITCH_SANDBOX_KEY` is not set so contributors
 * without sandbox access can still run the unit + drift suites.
 *
 * Each subclass shares the same merchant-tier client and a per-class
 * unique prefix so concurrent runs and re-runs don't collide on resource
 * IDs. tearDown is best-effort — failures during cleanup are logged but
 * don't fail the test (sandbox state may have drifted).
 */
abstract class AbstractIntegrationTestCase extends TestCase
{
    protected HyperSwitchClient $client;
    protected string $prefix;
    protected ?string $profileId = null;

    /** @var list<callable():void> */
    private array $cleanupCallbacks = [];

    protected function setUp(): void
    {
        $key = getenv('HYPERSWITCH_SANDBOX_KEY');
        if ($key === false || $key === '') {
            self::markTestSkipped('HYPERSWITCH_SANDBOX_KEY is not set; skipping sandbox integration test.');
        }

        $psr17 = new Psr17Factory();
        $this->client = HyperSwitchClient::sandbox(
            httpClient:     new Psr18Client(),
            requestFactory: $psr17,
            streamFactory:  $psr17,
            auth:           [
                new MerchantKeyAuthResolver($key),
                // V1 endpoints (PaymentMethods, PaymentMethodSessions) authenticate
                // the same key under a different header format. Register both so
                // tests can exercise either tier without re-wiring the client.
                new MerchantKeyV1AuthResolver($key),
            ],
            middleware:     [
                new IdempotencyKeyMiddleware(),
                new RetryMiddleware(maxAttempts: 2, baseDelayMs: 100),
            ],
        );

        $this->prefix    = 'sdkit_' . bin2hex(random_bytes(4));
        $profileId       = getenv('HYPERSWITCH_SANDBOX_PROFILE_ID');
        $this->profileId = ($profileId !== false && $profileId !== '') ? $profileId : null;
    }

    protected function tearDown(): void
    {
        // Run cleanups in LIFO so resource A created before B deletes B then A.
        foreach (array_reverse($this->cleanupCallbacks) as $callback) {
            try {
                $callback();
            } catch (\Throwable $e) {
                fwrite(STDERR, "[integration cleanup] {$e->getMessage()}\n");
            }
        }
        $this->cleanupCallbacks = [];
    }

    /**
     * Register a best-effort cleanup callback. Failures are logged to STDERR
     * but don't fail the test — sandbox state may have drifted.
     */
    protected function onTearDown(callable $callback): void
    {
        $this->cleanupCallbacks[] = $callback;
    }

    /**
     * Per-test unique resource id, scoped under this run's prefix.
     */
    protected function uniqueId(string $kind = 'res'): string
    {
        return $this->prefix . '_' . $kind . '_' . bin2hex(random_bytes(3));
    }
}
