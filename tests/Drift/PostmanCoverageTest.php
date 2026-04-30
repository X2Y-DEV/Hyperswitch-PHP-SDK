<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Drift;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Drift detection — asserts the SDK still mirrors the pinned Postman export.
 *
 * Failure modes:
 *  - Postman lists an endpoint with no matching Endpoint definition in src/
 *  - src/ contains a framework import that violates the architectural rule
 *  - (TODO) Request/Response DTOs missing for endpoints that take/return bodies
 *  - (TODO) Enum cases missing for closed-set fields used in Postman examples
 *
 * Run during the v0.1 phase as a guide for what AI maintenance must fill in.
 * After the SDK is fully populated, this becomes the gate that fails CI when
 * Hyperswitch ships a change.
 */
final class PostmanCoverageTest extends TestCase
{
    private const POSTMAN_PATH      = __DIR__ . '/../Fixtures/postman.json';
    private const SRC_DIR           = __DIR__ . '/../../src';
    private const ENDPOINT_DIR      = __DIR__ . '/../../src/Endpoint';
    private const FORBIDDEN_PREFIXES = ['Magento\\', 'Laravel\\', 'Symfony\\'];
    /** Symfony PSR adapter packages may legitimately appear in dev examples; keep the
     *  rule scoped to src/ and allow PSR namespaces. */

    public function testPostmanFixtureIsReadable(): void
    {
        self::assertFileExists(self::POSTMAN_PATH);
        $raw = file_get_contents(self::POSTMAN_PATH);
        self::assertIsString($raw);
        $decoded = json_decode($raw, true);
        self::assertIsArray($decoded);
        self::assertArrayHasKey('item', $decoded);
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
     * Walks Postman, collects every (METHOD, /normalised/path) pair, and reports
     * which Endpoint definitions are still missing. Currently informational
     * (the SDK is being populated by AI in Phase 3).
     */
    public function testEveryPostmanEndpointIsCoveredOrTrackedAsTodo(): void
    {
        $postman = json_decode((string) file_get_contents(self::POSTMAN_PATH), true);
        self::assertIsArray($postman);
        $expected = $this->extractEndpointsFromPostman($postman['item'] ?? []);

        $registered = $this->discoverRegisteredEndpoints();

        $missing = array_values(array_diff($expected, $registered));
        sort($missing);

        // Phase 1 expectation: only the template (Payments::create, Payments::retrieve)
        // is covered. Phase 3 (AI-driven coverage expansion) drives this list to zero.
        self::assertGreaterThan(
            0,
            count($expected),
            'Postman fixture appears to contain no endpoints.'
        );

        if ($missing !== []) {
            // Don't fail yet — print as informational. Replace with assertEmpty()
            // once Phase 3 is complete.
            fwrite(STDERR, "\n[Drift] Endpoints not yet registered in src/Endpoint/ (" . count($missing) . " items):\n");
            foreach ($missing as $line) {
                fwrite(STDERR, "  - $line\n");
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return list<string> e.g. ["POST /payments", "GET /payments/{id}"]
     */
    private function extractEndpointsFromPostman(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            if (isset($item['item']) && is_array($item['item'])) {
                $out = array_merge($out, $this->extractEndpointsFromPostman($item['item']));
                continue;
            }
            if (!isset($item['request'])) {
                continue;
            }
            $request = $item['request'];
            if (!is_array($request)) {
                continue;
            }
            $method = isset($request['method']) ? (string) $request['method'] : 'GET';
            $rawUrl = $this->extractRawUrl($request['url'] ?? null);
            if ($rawUrl === null) {
                continue;
            }
            $path = $this->normalisePath($rawUrl);
            if ($path === null) {
                continue;
            }
            $out[] = $method . ' ' . $path;
        }
        return array_values(array_unique($out));
    }

    private function extractRawUrl(mixed $url): ?string
    {
        if (is_string($url)) {
            return $url;
        }
        if (is_array($url) && isset($url['raw']) && is_string($url['raw'])) {
            return $url['raw'];
        }
        return null;
    }

    /**
     * Strip the {{baseUrl}} prefix and query string, keep the path with
     * Postman placeholders ($id → {id}).
     */
    private function normalisePath(string $rawUrl): ?string
    {
        $url = preg_replace('/\{\{[^}]+}}/', '', $rawUrl);
        if (!is_string($url) || $url === '') {
            return null;
        }
        $url = preg_replace('/\?.*$/', '', $url) ?? $url;
        // Convert :id-style Postman params to {id}
        $url = preg_replace('/:([a-zA-Z_][a-zA-Z0-9_]*)/', '{$1}', $url) ?? $url;
        if (!str_starts_with($url, '/')) {
            return null;
        }
        return $url;
    }

    /**
     * Discover Endpoint definitions registered in src/Endpoint/.
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
            // Coarse parse: pull `method:` and `pathTemplate:` from named-arg
            // Endpoint constructors.
            if (preg_match_all(
                "/method:\s*'(?P<method>[A-Z]+)'.*?pathTemplate:\s*'(?P<path>[^']+)'/s",
                $contents,
                $matches,
                PREG_SET_ORDER
            ) > 0) {
                foreach ($matches as $m) {
                    $registered[] = $m['method'] . ' ' . $m['path'];
                }
            }
        }
        return array_values(array_unique($registered));
    }
}
