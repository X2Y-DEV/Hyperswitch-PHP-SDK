<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Exception;

/** Thrown for HTTP 401 / 403 responses from HyperSwitch. */
final class AuthenticationException extends HyperSwitchApiException
{
}
