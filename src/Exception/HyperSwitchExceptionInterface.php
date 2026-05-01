<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Exception;

/**
 * Marker interface — every exception thrown by the SDK implements this.
 *
 * Consumers can catch `HyperSwitchExceptionInterface` to handle any SDK error,
 * or catch a specific subclass for finer control.
 */
interface HyperSwitchExceptionInterface extends \Throwable
{
}
