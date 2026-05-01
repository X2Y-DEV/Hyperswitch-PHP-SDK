<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Exception;

/**
 * Thrown when the request never reached HyperSwitch (DNS failure, connect
 * timeout, TLS error, etc.). Distinct from API errors because no HyperSwitch
 * response body exists.
 */
final class TransportException extends \RuntimeException implements HyperSwitchExceptionInterface
{
}
