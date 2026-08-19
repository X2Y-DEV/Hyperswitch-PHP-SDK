<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Drift;

use PHPUnit\Framework\TestCase;

/**
 * Drift detection — asserts the SDK still mirrors the pinned HyperSwitch v1
 * OpenAPI spec (tests/Fixtures/openapi_spec_v1.json).
 *
 * This replaces the Postman-based drift gate. The OpenAPI spec is the single
 * mechanical source of truth: it is first-party, versioned, and pinned to a
 * hyperswitch commit by tools/update-openapi.sh.
 *
 * Two directional gates:
 *   1. testEveryV1OperationIsCovered      — every spec operation is either
 *      registered in src/Endpoint/ or listed in EXCLUDED (a categorized
 *      allow-list of not-yet-modelled operations, which Phases 1–3 burn down).
 *   2. testNoUnexpectedSdkOnlyEndpoints   — every SDK endpoint is either in the
 *      v1 spec or in SDK_ONLY_ALLOWED (v2-compat paths + documented exceptions).
 *      This catches genuine drift (e.g. a wrong HTTP method) mechanically.
 *
 * Plus the architectural framework-import ban, ported here so it survives the
 * eventual removal of PostmanCoverageTest.
 */
final class OpenApiCoverageTest extends TestCase
{
    private const SPEC_PATH          = __DIR__ . '/../Fixtures/openapi_spec_v1.json';
    private const SRC_DIR            = __DIR__ . '/../../src';
    private const ENDPOINT_DIR       = __DIR__ . '/../../src/Endpoint';
    private const FORBIDDEN_PREFIXES = ['Magento\\', 'Laravel\\', 'Symfony\\'];
    private const HTTP_METHODS       = ['get', 'post', 'put', 'patch', 'delete'];

    /**
     * v1 spec operations the SDK does not model YET. Each value is a category
     * tag ("gap-<priority>-<group>") so the burndown is legible and grep-able.
     * Phases 1–3 of the v1-alignment work remove entries here as each group's
     * Endpoints/DTOs/Resource/tests land. Keys are canonical "METHOD /path"
     * (see canonicalisePath): every {placeholder} is collapsed to {id}.
     *
     * When the SDK genuinely should not model an operation, keep it here with a
     * deliberate reason instead of a "gap-*" tag. Do NOT delete a key to silence
     * a failure — model the endpoint, or justify the exclusion.
     *
     * @var array<string, string>
     */
    private const EXCLUDED = [
        // --- gap-p1-customers (1) ---
        'GET /v1/customers/reference/{id}' => 'gap-p1-customers',
        // --- gap-p1-disputes (8) ---
        'DELETE /disputes/evidence' => 'gap-p1-disputes',
        'GET /disputes/aggregate' => 'gap-p1-disputes',
        'GET /disputes/evidence/{id}' => 'gap-p1-disputes',
        'GET /disputes/filter' => 'gap-p1-disputes',
        'GET /disputes/profile/aggregate' => 'gap-p1-disputes',
        'GET /disputes/profile/filter' => 'gap-p1-disputes',
        'GET /disputes/profile/list' => 'gap-p1-disputes',
        'PUT /disputes/evidence' => 'gap-p1-disputes',
        // --- gap-p1-organization (2) ---
        'GET /organization/{id}' => 'gap-p1-organization',
        'PUT /organization/{id}' => 'gap-p1-organization',
        // --- gap-p1-payment-methods (9) — the v1 payment-methods surface ---
        'DELETE /payment_methods/{id}' => 'gap-p1-payment-methods',
        'GET /account/payment_methods' => 'gap-p1-payment-methods',
        'GET /customers/payment_methods' => 'gap-p1-payment-methods',
        'GET /customers/{id}/payment_methods' => 'gap-p1-payment-methods',
        'GET /payment_methods/{id}' => 'gap-p1-payment-methods',
        'GET /payments/{id}/client' => 'gap-p1-payment-methods',
        'POST /payment_methods' => 'gap-p1-payment-methods',
        'POST /payment_methods/{id}/update' => 'gap-p1-payment-methods',
        'POST /{id}/payment_methods/{id}/default' => 'gap-p1-payment-methods',
        // --- gap-p1-payments (7) ---
        'GET /payments/{id}/cancel_post_capture' => 'gap-p1-payments',
        'POST /payments/{id}/3ds/authentication' => 'gap-p1-payments',
        'POST /payments/{id}/complete_authorize' => 'gap-p1-payments',
        'POST /payments/{id}/eligibility' => 'gap-p1-payments',
        'POST /payments/{id}/eligibility_check' => 'gap-p1-payments',
        'POST /payments/{id}/post_session_tokens' => 'gap-p1-payments',
        'POST /payments/{id}/update_metadata' => 'gap-p1-payments',
        // --- gap-p1-payouts (3) ---
        'GET /payouts/list' => 'gap-p1-payouts',
        'POST /payouts/filter' => 'gap-p1-payouts',
        'POST /payouts/list' => 'gap-p1-payouts',
        // --- gap-p2-account (2) ---
        'POST /account/{id}/webhooks/{id}' => 'gap-p2-account',
        'POST /accounts/{id}/kv' => 'gap-p2-account',
        // --- gap-p2-authentication (8) ---
        'GET /authentication/{id}/eligibility-check' => 'gap-p2-authentication',
        'POST /authentication' => 'gap-p2-authentication',
        'POST /authentication/{id}/authenticate' => 'gap-p2-authentication',
        'POST /authentication/{id}/eligibility' => 'gap-p2-authentication',
        'POST /authentication/{id}/eligibility-check' => 'gap-p2-authentication',
        'POST /authentication/{id}/enabled_authn_methods_token' => 'gap-p2-authentication',
        'POST /authentication/{id}/redirect' => 'gap-p2-authentication',
        'POST /authentication/{id}/sync' => 'gap-p2-authentication',
        // --- gap-p2-events (4) ---
        'GET /events/{id}/{id}/attempts' => 'gap-p2-events',
        'POST /events/profile/list' => 'gap-p2-events',
        'POST /events/{id}' => 'gap-p2-events',
        'POST /events/{id}/{id}/retry' => 'gap-p2-events',
        // --- gap-p2-misc (2) ---
        'GET /poll/status/{id}' => 'gap-p2-misc',
        'POST /three_ds_decision/execute' => 'gap-p2-misc',
        // --- gap-p2-routing (20) ---
        'GET /routing' => 'gap-p2-routing',
        'GET /routing/active' => 'gap-p2-routing',
        'GET /routing/default' => 'gap-p2-routing',
        'GET /routing/default/profile' => 'gap-p2-routing',
        'GET /routing/{id}' => 'gap-p2-routing',
        'PATCH /account/{id}/business_profile/{id}/dynamic_routing/contracts/config/{id}' => 'gap-p2-routing',
        'PATCH /account/{id}/business_profile/{id}/dynamic_routing/success_based/config/{id}' => 'gap-p2-routing',
        'POST /account/{id}/business_profile/{id}/dynamic_routing/contracts/toggle' => 'gap-p2-routing',
        'POST /account/{id}/business_profile/{id}/dynamic_routing/elimination/create' => 'gap-p2-routing',
        'POST /account/{id}/business_profile/{id}/dynamic_routing/elimination/toggle' => 'gap-p2-routing',
        'POST /account/{id}/business_profile/{id}/dynamic_routing/success_based/create' => 'gap-p2-routing',
        'POST /account/{id}/business_profile/{id}/dynamic_routing/success_based/toggle' => 'gap-p2-routing',
        'POST /routing' => 'gap-p2-routing',
        'POST /routing/deactivate' => 'gap-p2-routing',
        'POST /routing/default' => 'gap-p2-routing',
        'POST /routing/default/profile/{id}' => 'gap-p2-routing',
        'POST /routing/evaluate' => 'gap-p2-routing',
        'POST /routing/feedback' => 'gap-p2-routing',
        'POST /routing/rule/evaluate' => 'gap-p2-routing',
        'POST /routing/{id}/activate' => 'gap-p2-routing',
        // --- gap-p2-subscriptions (1) ---
        'GET /subscriptions/items' => 'gap-p2-subscriptions',
        // --- gap-p3-blocklist (7) ---
        'DELETE /blocklist' => 'gap-p3-blocklist',
        'GET /blocklist' => 'gap-p3-blocklist',
        'GET /blocklist/batch' => 'gap-p3-blocklist',
        'GET /blocklist/batch/{id}' => 'gap-p3-blocklist',
        'POST /blocklist' => 'gap-p3-blocklist',
        'POST /blocklist/batch' => 'gap-p3-blocklist',
        'POST /blocklist/toggle' => 'gap-p3-blocklist',
        // --- gap-p3-card-issuers (4) ---
        'DELETE /card_issuers/{id}' => 'gap-p3-card-issuers',
        'GET /card_issuers' => 'gap-p3-card-issuers',
        'POST /card_issuers' => 'gap-p3-card-issuers',
        'PUT /card_issuers/{id}' => 'gap-p3-card-issuers',
        // --- gap-p3-gsm (4) ---
        'POST /gsm' => 'gap-p3-gsm',
        'POST /gsm/delete' => 'gap-p3-gsm',
        'POST /gsm/get' => 'gap-p3-gsm',
        'POST /gsm/update' => 'gap-p3-gsm',
        // --- gap-p3-profile-acquirers (2) ---
        'POST /profile_acquirers' => 'gap-p3-profile-acquirers',
        'POST /profile_acquirers/{id}/{id}' => 'gap-p3-profile-acquirers',
    ];

    /**
     * SDK endpoints deliberately absent from the v1 spec. Keys are canonical
     * "METHOD /path"; values document why.
     *
     *  - v2-compat: the /v1/payment-method(s|-sessions) + /v1/proxy surface is
     *    the v2 API's modular payment-methods flow (present in openapi_spec_v2,
     *    NOT v1). It belongs to the future, separate v2 SDK; kept here pragmatically.
     *  - pending-reconciliation / real-but-undocumented: endpoints the SDK ships
     *    that the v1 spec does not list. Verify against the sandbox and either
     *    reconcile (rename/fix) or promote to a permanent, reasoned exception.
     *
     * @var array<string, string>
     */
    private const SDK_ONLY_ALLOWED = [
        // v2-compat modular payment-methods surface (future v2 SDK)
        'DELETE /v1/payment-method-sessions/{id}' => 'v2-compat',
        'DELETE /v1/payment-methods/{id}' => 'v2-compat',
        'GET /v1/customers/{id}/saved-payment-methods' => 'v2-compat',
        'GET /v1/payment-method-sessions/{id}' => 'v2-compat',
        'GET /v1/payment-method-sessions/{id}/list-payment-methods' => 'v2-compat',
        'GET /v1/payment-methods/token/{id}/details' => 'v2-compat',
        'GET /v1/payment-methods/{id}' => 'v2-compat',
        'POST /v1/payment-method-sessions' => 'v2-compat',
        'POST /v1/payment-method-sessions/{id}/confirm' => 'v2-compat',
        'POST /v1/payment-methods' => 'v2-compat',
        'POST /v1/proxy' => 'v2-compat',
        'PUT /v1/payment-method-sessions/{id}/update-saved-payment-method' => 'v2-compat',
        'PUT /v1/payment-methods/{id}/update-saved-payment-method' => 'v2-compat',
        // Not in v1 spec — pending sandbox reconciliation (see docs/v1-alignment.md, Group A2)
        'GET /subscriptions/list' => 'pending-reconciliation: v1 spec has GET /subscriptions/items instead',
        'GET /subscriptions/plans' => 'pending-reconciliation: not in v1 spec',
        'POST /api/organization/{id}/convert_to_platform' => 'pending-reconciliation: /api-prefixed, not in v1 spec',
        'POST /ephemeral_keys' => 'real-but-undocumented: ephemeral_key scheme exists but no documented path',
    ];

    public function testSpecFixtureIsReadable(): void
    {
        self::assertFileExists(self::SPEC_PATH);
        $raw = file_get_contents(self::SPEC_PATH);
        self::assertIsString($raw);
        $decoded = json_decode($raw, true);
        self::assertIsArray($decoded);
        self::assertArrayHasKey('paths', $decoded);
        self::assertArrayHasKey('components', $decoded);
        self::assertArrayHasKey('schemas', $decoded['components']);
    }

    public function testNoForbiddenFrameworkImportsUnderSrc(): void
    {
        $offenders = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::SRC_DIR, \FilesystemIterator::SKIP_DOTS),
        );
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $contents = (string) file_get_contents($file->getPathname());
            foreach (self::FORBIDDEN_PREFIXES as $prefix) {
                if (preg_match('/\buse\s+' . preg_quote($prefix, '/') . '/', $contents) === 1) {
                    $offenders[] = sprintf('%s imports forbidden namespace %s', $file->getPathname(), $prefix);
                }
            }
        }
        self::assertEmpty(
            $offenders,
            "Framework imports detected under src/:\n" . implode("\n", $offenders)
        );
    }

    /**
     * Every v1 spec operation must be registered in src/Endpoint/ or explicitly
     * excluded. Failures are genuine coverage gaps — model them (or, if truly
     * out of scope, add them to EXCLUDED with a reason).
     */
    public function testEveryV1OperationIsCovered(): void
    {
        $expected   = $this->extractOperationsFromSpec();
        self::assertGreaterThan(0, count($expected), 'Spec fixture appears to contain no operations.');

        $registered = $this->discoverRegisteredEndpoints();
        $missing    = array_values(array_diff($expected, $registered, array_keys(self::EXCLUDED)));
        sort($missing);

        self::assertEmpty(
            $missing,
            "v1 spec operations not registered in src/Endpoint/ and not on the EXCLUDED allow-list:\n  - "
                . implode("\n  - ", $missing)
                . "\n\nModel these, or if genuinely out of scope add them to OpenApiCoverageTest::EXCLUDED with a reason."
        );
    }

    /**
     * Every SDK endpoint must exist in the v1 spec or be an acknowledged
     * exception. Catches drift such as a wrong HTTP method or a stale path.
     */
    public function testNoUnexpectedSdkOnlyEndpoints(): void
    {
        $registered = $this->discoverRegisteredEndpoints();
        $spec       = $this->extractOperationsFromSpec();

        $sdkOnly = array_values(array_diff($registered, $spec, array_keys(self::SDK_ONLY_ALLOWED)));
        sort($sdkOnly);

        self::assertEmpty(
            $sdkOnly,
            "SDK endpoints not present in the v1 spec and not on the SDK_ONLY_ALLOWED list:\n  - "
                . implode("\n  - ", $sdkOnly)
                . "\n\nThis is drift. Fix the endpoint to match the spec, or (if intentional, e.g. a"
                . " real-but-undocumented route) add it to OpenApiCoverageTest::SDK_ONLY_ALLOWED with a reason."
        );
    }

    /**
     * Walk paths × HTTP methods → canonical "METHOD /path" list.
     *
     * @return list<string>
     */
    private function extractOperationsFromSpec(): array
    {
        $spec = json_decode((string) file_get_contents(self::SPEC_PATH), true);
        self::assertIsArray($spec);
        $paths = $spec['paths'] ?? [];
        self::assertIsArray($paths);

        $out = [];
        foreach ($paths as $path => $item) {
            if (!is_string($path) || !is_array($item)) {
                continue;
            }
            foreach (self::HTTP_METHODS as $method) {
                if (isset($item[$method]) && is_array($item[$method])) {
                    $out[] = self::canonicalisePath($method, $path);
                }
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * Discover Endpoint definitions registered in src/Endpoint/ by pulling
     * `method:` and `pathTemplate:` out of the named-arg Endpoint constructors.
     *
     * @return list<string>
     */
    private function discoverRegisteredEndpoints(): array
    {
        $registered = [];
        if (!is_dir(self::ENDPOINT_DIR)) {
            return $registered;
        }
        foreach (glob(self::ENDPOINT_DIR . '/*.php') ?: [] as $file) {
            $contents = (string) file_get_contents($file);
            if (preg_match_all(
                "/method:\s*'(?P<method>[A-Z]+)'.*?pathTemplate:\s*'(?P<path>[^']+)'/s",
                $contents,
                $matches,
                PREG_SET_ORDER
            ) > 0) {
                foreach ($matches as $m) {
                    $registered[] = self::canonicalisePath($m['method'], $m['path']);
                }
            }
        }
        return array_values(array_unique($registered));
    }

    /**
     * Canonical "METHOD /path" form for symmetric comparison. Unlike the older
     * Postman canonicaliser, this collapses EVERY {placeholder} segment to
     * {id} — the spec names params ({payout_id}, {profile_id}, {account_id})
     * differently from the SDK ({id}, {customer_id}), so keeping any original
     * name would produce false drift on multi-param paths.
     */
    public static function canonicalisePath(string $method, string $path): string
    {
        $path = preg_replace('/\?.*$/', '', $path) ?? $path;
        $segments = explode('/', $path);
        foreach ($segments as $i => $segment) {
            if (preg_match('/^\{[^}]+}$/', $segment) === 1) {
                $segments[$i] = '{id}';
            }
        }
        return strtoupper($method) . ' ' . implode('/', $segments);
    }
}
