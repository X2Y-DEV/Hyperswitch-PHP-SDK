<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Exception;

/** Thrown for HTTP 429 responses. Honours the `Retry-After` header when present. */
final class RateLimitedException extends HyperswitchApiException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        ?int $httpStatus = 429,
        array $payload = [],
        public readonly ?int $retryAfterSeconds = null,
    ) {
        parent::__construct($message, $code, $previous, $httpStatus, $payload);
    }
}
