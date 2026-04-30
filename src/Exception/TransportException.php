<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Exception;

/**
 * Thrown when the request never reached Hyperswitch (DNS failure, connect
 * timeout, TLS error, etc.). Distinct from API errors because no Hyperswitch
 * response body exists.
 */
final class TransportException extends \RuntimeException implements HyperswitchExceptionInterface
{
}
