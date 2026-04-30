<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Exception;

/**
 * Marker interface — every exception thrown by the SDK implements this.
 *
 * Consumers can catch `HyperswitchExceptionInterface` to handle any SDK error,
 * or catch a specific subclass for finer control.
 */
interface HyperswitchExceptionInterface extends \Throwable
{
}
