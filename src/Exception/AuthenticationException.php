<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Exception;

/** Thrown for HTTP 401 / 403 responses from Hyperswitch. */
final class AuthenticationException extends HyperswitchApiException
{
}
