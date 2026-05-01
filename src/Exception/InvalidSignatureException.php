<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Exception;

/**
 * Thrown when a webhook signature fails HMAC verification, when the
 * signature header is missing on a request that should be signed, or when
 * no hash key is configured on the verifier.
 *
 * Receivers SHOULD respond with HTTP 401 to a request that triggers this
 * exception. Do not echo the exception message back to the caller — it
 * may include the canonical input that was signed.
 */
final class InvalidSignatureException extends \RuntimeException implements HyperswitchExceptionInterface
{
}
