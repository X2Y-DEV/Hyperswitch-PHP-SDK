<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Integration;

use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;
use X2Y\HyperSwitchClient\Auth\AdminKeyAuthResolver;
use X2Y\HyperSwitchClient\Auth\EphemeralKeyAuthResolver;
use X2Y\HyperSwitchClient\Auth\JwtKeyAuthResolver;
use X2Y\HyperSwitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperSwitchClient\Auth\MerchantKeyV1AuthResolver;
use X2Y\HyperSwitchClient\Auth\PublishableKeyAuthResolver;
use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationCreateRequest;
use X2Y\HyperSwitchClient\Dto\Connector\Request\ConnectorWebhookRegisterRequest;
use X2Y\HyperSwitchClient\Dto\Event\Request\EventListConstraintsRequest;
use X2Y\HyperSwitchClient\Dto\Gsm\Request\GsmKeyRequest;
use X2Y\HyperSwitchClient\Dto\MerchantAccount\Request\ToggleKvRequest;
use X2Y\HyperSwitchClient\Dto\Payout\Request\PayoutFiltersRequest;
use X2Y\HyperSwitchClient\Dto\ProfileAcquirer\Request\ProfileAcquirerUpdateRequest;
use X2Y\HyperSwitchClient\Dto\ThreeDsDecision\Request\ThreeDsDecisionExecuteRequest;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\HyperSwitchClient;
use X2Y\HyperSwitchClient\Transport\Middleware\IdempotencyKeyMiddleware;
use X2Y\HyperSwitchClient\Transport\Middleware\RetryMiddleware;

/**
 * Wiring smoke test for the groups/operations added during the v1 alignment.
 *
 * These endpoints have no per-operation integration coverage, and many can't be
 * fully exercised on an arbitrary sandbox account (admin/JWT tiers, platform-
 * account restrictions, profile-scoped headers). So rather than assert business
 * outcomes, each call asserts it *reaches HyperSwitch*: the SDK builds a valid
 * request, sends it, and the API returns a structured response or a mapped error
 * — NOT a "route not found" (IR_02, which would mean a mis-wired path/method) and
 * NOT a transport-level crash. Auth/validation/not-found/platform errors all pass.
 *
 * Skips cleanly without HYPERSWITCH_SANDBOX_KEY. Optional:
 *   HYPERSWITCH_SANDBOX_PUBLISHABLE_KEY  (else the merchant key is reused)
 *   HYPERSWITCH_SANDBOX_PROFILE_ID       (attached to profile-scoped calls)
 */
final class NewGroupsSmokeIntegrationTest extends TestCase
{
    /**
     * Endpoints present in the pinned v1 spec but not yet routed on the public
     * sandbox — they return IR_02 even at their spec-exact path (verified via
     * curl 2026-08-19). These are deployment gaps, not SDK wiring bugs, so an
     * IR_02 here is tolerated; an IR_02 from any other label fails the test.
     *
     * @var list<string>
     */
    private const SPEC_AHEAD_OF_SANDBOX = [
        'ProfileAcquirer::update',
        'Connectors::registerWebhook',
    ];

    private HyperSwitchClient $client;
    private ?string $profileId = null;

    protected function setUp(): void
    {
        $key = getenv('HYPERSWITCH_SANDBOX_KEY');
        if ($key === false || $key === '') {
            self::markTestSkipped('HYPERSWITCH_SANDBOX_KEY is not set; skipping sandbox integration test.');
        }

        $pub = getenv('HYPERSWITCH_SANDBOX_PUBLISHABLE_KEY');
        $pub = ($pub !== false && $pub !== '') ? $pub : $key;
        $profileId = getenv('HYPERSWITCH_SANDBOX_PROFILE_ID');
        $this->profileId = ($profileId !== false && $profileId !== '') ? $profileId : null;

        $psr17 = new Psr17Factory();
        $this->client = HyperSwitchClient::sandbox(
            httpClient:     new Psr18Client(),
            requestFactory: $psr17,
            streamFactory:  $psr17,
            // Every tier is registered so each call reaches the API. Where a
            // dedicated key isn't available the merchant key (or a placeholder
            // JWT) is used — a 401 still proves the route is wired.
            auth: [
                new MerchantKeyAuthResolver($key),
                new MerchantKeyV1AuthResolver($key),
                new PublishableKeyAuthResolver($pub),
                new AdminKeyAuthResolver($key),
                new EphemeralKeyAuthResolver($key),
                new JwtKeyAuthResolver('placeholder.jwt.token'),
            ],
            middleware: [
                new IdempotencyKeyMiddleware(),
                new RetryMiddleware(maxAttempts: 2, baseDelayMs: 100),
            ],
        );
    }

    /**
     * Every added endpoint must reach HyperSwitch (no route-not-found, no crash).
     * Runs all calls and reports every wiring failure together.
     */
    public function testEveryNewEndpointReachesTheApi(): void
    {
        $c = $this->client;

        /** @var array<string, callable():mixed> $calls */
        $calls = [
            // --- Routing ---
            'Routing::list'                 => static fn () => $c->routing()->list(),
            'Routing::active'               => static fn () => $c->routing()->active(),
            'Routing::defaultConfig'        => static fn () => $c->routing()->defaultConfig(),
            // --- Authentication (3DS) ---
            'Authentication::create'        => static fn () => $c->authentication()->create(new AuthenticationCreateRequest()),
            // --- Events (JWT) ---
            'Events::listForProfile'        => static fn () => $c->events()->listForProfile(new EventListConstraintsRequest()),
            'Events::attempts'              => static fn () => $c->events()->attempts('mer_smoke', 'evt_smoke'),
            // --- Blocklist ---
            'Blocklist::list'               => static fn () => $c->blocklist()->list(),
            'Blocklist::listBatch'          => static fn () => $c->blocklist()->listBatch(),
            // --- GSM (admin) ---
            'Gsm::retrieve'                 => static fn () => $c->gsm()->retrieve(new GsmKeyRequest('stripe', 'authorize', 'payment', '00', 'ok')),
            // --- Card Issuers ---
            'CardIssuer::list'              => static fn () => $c->cardIssuer()->list(),
            // --- Profile Acquirers (update nonexistent — no resource created) ---
            'ProfileAcquirer::update'       => static fn () => $c->profileAcquirer()->update('pro_smoke', 'pa_smoke', new ProfileAcquirerUpdateRequest()),
            // --- Poll (publishable) ---
            'Poll::status'                  => static fn () => $c->poll()->status('poll_smoke'),
            // --- 3DS Decision Rule ---
            'ThreeDsDecision::executeRule'  => static fn () => $c->threeDsDecision()->executeRule(new ThreeDsDecisionExecuteRequest(routingId: 'rt_smoke', payment: [])),
            // --- v1 Payment Methods surface ---
            'PaymentMethodsV1::listForMerchant' => static fn () => $c->paymentMethodsV1()->listForMerchant(),
            // --- Payments extras ---
            'Payments::cancelPostCaptureRetrieve' => static fn () => $c->payments()->cancelPostCaptureRetrieve('pay_smoke'),
            // --- Payouts list/filter ---
            'Payouts::filters'              => static fn () => $c->payouts()->filters(new PayoutFiltersRequest(new \DateTimeImmutable('-1 day'))),
            'Payouts::list'                 => static fn () => $c->payouts()->list(),
            // --- Disputes filters/aggregate ---
            'Disputes::filters'             => static fn () => $c->disputes()->filters(),
            // --- Organization ---
            'Platform::retrieveOrganization' => static fn () => $c->platform()->retrieveOrganization('org_smoke'),
            // --- MerchantAccounts KV (admin) ---
            'MerchantAccounts::toggleKv'    => static fn () => $c->merchantAccounts()->toggleKv('mer_smoke', new ToggleKvRequest(true)),
            // --- Connectors webhook register ---
            'Connectors::registerWebhook'   => static fn () => $c->connectors()->registerWebhook('mer_smoke', 'mca_smoke', new ConnectorWebhookRegisterRequest()),
            // --- Subscriptions items (profile-scoped) ---
            'Subscriptions::items'          => fn () => $this->subscriptionsItems(),
        ];

        $failures = [];
        $notDeployed = [];
        foreach ($calls as $label => $call) {
            try {
                $call();
                // returned normally — reached the API
            } catch (HyperSwitchApiException $e) {
                if ($this->isRouteNotFound($e)) {
                    if (in_array($label, self::SPEC_AHEAD_OF_SANDBOX, true)) {
                        $notDeployed[] = $label;
                    } else {
                        $failures[] = sprintf('%s — route not found (mis-wired path/method): %s', $label, $e->getMessage());
                    }
                }
                // any other mapped API error (auth / validation / not-found /
                // platform restriction) means the request reached HyperSwitch — OK.
            } catch (\Throwable $e) {
                $failures[] = sprintf('%s — unexpected %s: %s', $label, $e::class, $e->getMessage());
            }
        }

        if ($notDeployed !== []) {
            fwrite(STDERR, "[smoke] spec-ahead-of-sandbox (IR_02, not yet deployed): " . implode(', ', $notDeployed) . "\n");
        }

        self::assertSame(
            [],
            $failures,
            "New endpoints that did not reach the API cleanly:\n  - " . implode("\n  - ", $failures)
        );
    }

    private function subscriptionsItems(): mixed
    {
        $resource = $this->client->subscriptions();
        if ($this->profileId !== null) {
            $resource = $resource->withProfileId($this->profileId);
        }
        return $resource->items(['item_type' => 'plan']);
    }

    /**
     * A HyperSwitch 404 for an unknown *route* (IR_02 / "Unrecognized request
     * URL") signals a mis-wired path/method. A 404 for a missing *resource* is
     * expected here and does not count.
     */
    private function isRouteNotFound(HyperSwitchApiException $e): bool
    {
        if (str_contains($e->getMessage(), 'Unrecognized request URL')) {
            return true;
        }
        $code = $this->extractErrorCode($e->payload);
        return $code === 'IR_02';
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractErrorCode(array $payload): ?string
    {
        $error = $payload['error'] ?? null;
        if (is_array($error) && isset($error['code']) && is_string($error['code'])) {
            return $error['code'];
        }
        if (isset($payload['code']) && is_string($payload['code'])) {
            return $payload['code'];
        }
        return null;
    }
}
