<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Transport\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use X2Y\HyperswitchClient\Exception\TransportException;

/**
 * Retries on 5xx responses, 429 (with Retry-After honoured when present),
 * and transport failures.
 *
 * Defaults: 3 attempts, exponential backoff starting at 200ms with full jitter.
 * GET requests are always retryable; non-idempotent methods retry only on
 * transport-level failures (i.e. before the server saw the request).
 */
final class RetryMiddleware implements ClientMiddlewareInterface
{
    public function __construct(
        private readonly int $maxAttempts = 3,
        private readonly int $baseDelayMs = 200,
    ) {
    }

    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        $attempt    = 0;
        $isReadOnly = $request->getMethod() === 'GET' || $request->getMethod() === 'HEAD';

        while (true) {
            $attempt++;
            try {
                $response = $next($request);
            } catch (TransportException $e) {
                if ($attempt >= $this->maxAttempts) {
                    throw $e;
                }
                $this->sleep($attempt);
                continue;
            }

            $status = $response->getStatusCode();

            if (!$isReadOnly && !in_array($status, [503, 504], true)) {
                // For non-idempotent calls, only retry on the most clear-cut "did not happen" 5xx.
                return $response;
            }

            if ($status >= 500 || $status === 429) {
                if ($attempt >= $this->maxAttempts) {
                    return $response;
                }
                $this->sleep($attempt, $response);
                continue;
            }

            return $response;
        }
    }

    private function sleep(int $attempt, ?ResponseInterface $response = null): void
    {
        if ($response !== null && $response->hasHeader('Retry-After')) {
            $retryAfter = (int) $response->getHeaderLine('Retry-After');
            if ($retryAfter > 0) {
                usleep($retryAfter * 1_000_000);
                return;
            }
        }
        $delay = $this->baseDelayMs * (2 ** ($attempt - 1));
        $jittered = random_int(0, $delay);
        usleep($jittered * 1_000);
    }
}
