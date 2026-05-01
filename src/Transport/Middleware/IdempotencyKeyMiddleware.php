<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Transport\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Adds an idempotency key header to mutating requests when the caller has
 * not already supplied one. The key is a UUIDv4 generated per call.
 *
 * The header name is configurable; HyperSwitch's documented header is
 * `X-Idempotency-Key` but check current docs before deploying.
 */
final class IdempotencyKeyMiddleware implements ClientMiddlewareInterface
{
    public function __construct(
        private readonly string $headerName = 'X-Idempotency-Key',
    ) {
    }

    public function process(RequestInterface $request, callable $next): ResponseInterface
    {
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            return $next($request);
        }
        if ($request->hasHeader($this->headerName)) {
            return $next($request);
        }
        return $next($request->withHeader($this->headerName, $this->generateKey()));
    }

    private function generateKey(): string
    {
        $bytes    = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40); // version 4
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80); // variant
        $hex      = bin2hex($bytes);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }
}
