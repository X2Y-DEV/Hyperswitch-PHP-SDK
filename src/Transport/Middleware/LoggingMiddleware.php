<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Transport\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Logs request method/URL and response status via PSR-3.
 *
 * Bodies are not logged by default — they may contain customer PII or partial
 * card data. Consumers who need body logging can subclass and opt in.
 */
final class LoggingMiddleware implements ClientMiddlewareInterface
{
    public function __construct(
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        $startedAt = microtime(true);
        $this->logger->debug('[hyperswitch] -> {method} {url}', [
            'method' => $request->getMethod(),
            'url'    => (string) $request->getUri(),
        ]);

        $response = $next($request);

        $this->logger->debug('[hyperswitch] <- {status} ({elapsed_ms}ms)', [
            'status'     => $response->getStatusCode(),
            'elapsed_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return $response;
    }
}
