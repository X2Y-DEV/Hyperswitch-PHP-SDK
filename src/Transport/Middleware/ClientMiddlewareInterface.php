<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Transport\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Onion-style middleware contract. Each middleware receives the outbound
 * request and a `$next` callable that dispatches it (after any inner
 * middleware) and returns the response.
 *
 * Example: idempotency-key middleware adds a header before calling `$next`;
 * retry middleware calls `$next` repeatedly with backoff on 5xx.
 *
 * @phpstan-type Next callable(RequestInterface): ResponseInterface
 */
interface ClientMiddlewareInterface
{
    /**
     * @param callable(RequestInterface): ResponseInterface $next
     */
    public function process(RequestInterface $request, callable $next): ResponseInterface;
}
