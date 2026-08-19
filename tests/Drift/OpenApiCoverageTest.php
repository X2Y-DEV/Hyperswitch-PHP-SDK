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
     * v1 spec operations the SDK deliberately does not model, keyed by canonical
     * "METHOD /path" (see canonicalisePath) with a reason value.
     *
     * The v1-alignment burndown is complete — the SDK now models all 150 v1
     * operations, so this list is empty. If HyperSwitch ships a new operation,
     * either model it (preferred) or add it here with a deliberate reason.
     * Do NOT add a "gap" entry just to silence a failure — model the endpoint.
     *
     * @var array<string, string>
     */
    private const EXCLUDED = [
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
