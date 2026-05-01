<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Exception;

/** Thrown for HTTP 400 / 422 responses (malformed request or business-rule violation). */
final class ValidationException extends HyperSwitchApiException
{
}
