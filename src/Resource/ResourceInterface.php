<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

/**
 * Marker interface for every Resource facade.
 *
 * Lets the top-level HyperswitchClient hold a typed registry of resources
 * and lets tests assert "every Postman group has a Resource implementation".
 */
interface ResourceInterface
{
}
