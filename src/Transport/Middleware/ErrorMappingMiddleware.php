<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Transport\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Reserved for cross-cutting error normalisation that should happen before
 * the ApiClient inspects the response (e.g. trace-id capture, telemetry
 * tagging on failure).
 *
 * The ApiClient already maps HTTP status → typed exception, so this
 * middleware is intentionally a no-op by default. Consumers can subclass
 * to add their own pre-decode hooks.
 */
class ErrorMappingMiddleware implements ClientMiddlewareInterface
{
    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        return $next($request);
    }
}
