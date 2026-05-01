<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

/**
 * Marker interface for every Resource facade.
 *
 * Lets the top-level HyperSwitchClient hold a typed registry of resources
 * and lets tests assert "every Postman group has a Resource implementation".
 */
interface ResourceInterface
{
}
