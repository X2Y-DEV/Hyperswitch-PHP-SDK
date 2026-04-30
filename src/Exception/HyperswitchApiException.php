<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Exception;

/**
 * Generic Hyperswitch API failure. Specific subclasses extend this for
 * authentication, validation, not-found, rate-limit, and transient errors.
 *
 * `$payload` carries the decoded error body when available, useful for
 * inspecting Hyperswitch's `error_code`, `error_type`, and `error_message`.
 */
class HyperswitchApiException extends \RuntimeException implements HyperswitchExceptionInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?int $httpStatus = null,
        public readonly array $payload = [],
    ) {
        parent::__construct($message, $code, $previous);
    }
}
