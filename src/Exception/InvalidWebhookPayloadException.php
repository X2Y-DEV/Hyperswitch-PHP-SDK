<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Exception;

/**
 * Thrown when a webhook body is well-signed but its JSON shape is missing
 * required fields (e.g. no `event_type`). Distinct from invalid-signature
 * because the request is authenticated — receivers SHOULD respond 400
 * (not 401) and log the body for forensics.
 */
final class InvalidWebhookPayloadException extends \RuntimeException implements HyperSwitchExceptionInterface
{
}
