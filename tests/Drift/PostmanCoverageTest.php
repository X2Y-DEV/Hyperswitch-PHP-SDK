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

    /**
     * Postman items the SDK intentionally does not model. Each entry is the
     * canonicalised "METHOD /path" form (after `normalisePath`).
     *
     * The legacy `/payment_methods` endpoints are skipped per the project's
     * "V1 only" decision — V1 paths under `/v1/payment-methods` are the
     * supported surface. See PLAN.md decisions log.
     */
    private const INTENTIONALLY_SKIPPED = [
        'POST /payment_methods',
        'POST /payment_methods/{id}',
        'GET /account/payment_methods',
        'GET /customers/{id}/payment_methods',
        'DELETE /payment_methods/{id}',
    ];

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
    public function testEveryPostmanEndpointIsCovered(): void
    {
        $postman = json_decode((string) file_get_contents(self::POSTMAN_PATH), true);
        self::assertIsArray($postman);
        $expected = $this->extractEndpointsFromPostman($postman['item'] ?? []);

        self::assertGreaterThan(
            0,
            count($expected),
            'Postman fixture appears to contain no endpoints.'
        );

        $registered = $this->discoverRegisteredEndpoints();
        $missing    = array_values(array_diff($expected, $registered, self::INTENTIONALLY_SKIPPED));
        sort($missing);

        self::assertEmpty(
            $missing,
            "Postman endpoints not registered in src/Endpoint/ and not on the intentional-skip list:\n  - "
                . implode("\n  - ", $missing)
                . "\n\nIf this is genuinely intentional, add it to PostmanCoverageTest::INTENTIONALLY_SKIPPED with a comment."
        );
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
     * placeholders normalised so SDK and Postman compare cleanly.
     *
     * Two normalisations matter for false-positive elimination:
     *
     *   1) Postman uses both `:name` and `{{name}}` for path params.
     *      We collapse both to the SDK's `{id}` form. Postman variables
     *      like `{{payment_method_id}}` previously stripped to empty
     *      segments — now they become `{id}` like everything else.
     *
     *   2) Postman names path params variously (`:id`, `:dispute_id`,
     *      `:payment_id`, `:customer_id`) while the SDK always uses `{id}`
     *      or `{customer_id}`. We normalise all `{<anything>_id}` and
     *      `{id}` to a single canonical `{id}`, EXCEPT for paths that
     *      genuinely need two distinct path params (e.g.
     *      `/api_keys/{merchant_id}/{api_key_id}`) — those keep the
     *      first and collapse the second to `{id}`. Same rule on both
     *      sides of the comparison so the heuristic is symmetric.
     */
    private function normalisePath(string $rawUrl): ?string
    {
        // Strip everything before the first '/' — Postman raw URLs prefix
        // the path with `{{baseUrl}}` or `{{base_url}}` placeholders that
        // aren't part of the path itself.
        $slash = strpos($rawUrl, '/');
        if ($slash === false) {
            return null;
        }
        $url = substr($rawUrl, $slash);

        // Strip query string.
        $url = preg_replace('/\?.*$/', '', $url) ?? $url;

        // Resolve Postman-style placeholders: {{name}} → {name}.
        $url = preg_replace('/\{\{([^}]+)}}/', '{$1}', $url) ?? $url;

        // Convert :id-style Postman params to {id}. Allow hyphens (e.g. :api-key)
        // so we don't strand them as empty segments.
        $url = preg_replace('/:([a-zA-Z_][a-zA-Z0-9_-]*)/', '{$1}', $url) ?? $url;

        if (!str_starts_with($url, '/') || $url === '/') {
            return null;
        }
        return self::canonicalisePathParams($url);
    }

    /**
     * Reduce path-param names to a canonical form so SDK ({id}) and
     * Postman (:dispute_id, :payment_id, …) compare equal.
     *
     * Heuristic: for paths with a single `{*_id}` placeholder, rename to
     * `{id}`. For paths with multiple placeholders, keep the first and
     * canonicalise subsequent ones to `{id}`. Hyphenated placeholders
     * like `{api-key}` are also collapsed to `{id}`.
     */
    public static function canonicalisePathParams(string $path): string
    {
        $segments = explode('/', $path);
        $placeholderCount = 0;
        foreach ($segments as $i => $segment) {
            if (preg_match('/^\{[^}]+}$/', $segment) === 1) {
                $placeholderCount++;
                // First placeholder keeps its name only when it's an "anchor"
                // like {merchant_id} that distinguishes the resource. The
                // *last* placeholder almost always names the leaf resource —
                // we collapse that to {id} regardless.
                $segments[$i] = '{id}';
            }
        }
        // If exactly one placeholder, the loop above already canonicalised it
        // to {id}. If multiple, we have all-{id} now — re-introduce the
        // first one's original name so /a/{merchant_id}/b/{id} is preserved.
        if ($placeholderCount > 1) {
            $original = explode('/', $path);
            $first = true;
            foreach ($original as $i => $segment) {
                if (preg_match('/^\{[^}]+}$/', $segment) === 1) {
                    if ($first) {
                        $segments[$i] = $segment;
                        $first = false;
                    }
                }
            }
        }
        return implode('/', $segments);
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
                    // Same canonicalisation as Postman side so a Postman
                    // `:dispute_id` and an SDK `{id}` compare equal.
                    $registered[] = $m['method'] . ' ' . self::canonicalisePathParams($m['path']);
                }
            }
        }
        return array_values(array_unique($registered));
    }
}
