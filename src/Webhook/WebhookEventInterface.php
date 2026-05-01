<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Webhook;

/**
 * Marker interface for inbound webhook events. Concrete implementations
 * carry a verified payload — receivers can rely on having authenticated
 * the call before this object is handed to business logic.
 */
interface WebhookEventInterface
{
}
