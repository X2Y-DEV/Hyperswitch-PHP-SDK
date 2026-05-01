<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Exception;

/**
 * Thrown for HTTP 5xx responses. The RetryMiddleware retries before this
 * surfaces to the consumer; consumers receiving this should treat the
 * call as failed but eligible for a future retry.
 */
final class TransientException extends HyperSwitchApiException
{
}
