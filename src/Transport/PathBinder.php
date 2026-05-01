<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Transport;

use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;

/**
 * Substitutes `{name}` placeholders in path templates with provided values
 * and appends query parameters.
 *
 * `bind('/payments/{id}/confirm', ['id' => 'pay_abc'])` → `/payments/pay_abc/confirm`
 * `bind('/payments/list', [], ['limit' => 50])` → `/payments/list?limit=50`
 */
final class PathBinder
{
    private function __construct()
    {
    }

    /**
     * @param array<string, scalar> $pathParams
     * @param array<string, scalar|array<string, scalar>> $queryParams
     */
    public static function bind(string $template, array $pathParams = [], array $queryParams = []): string
    {
        $path = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $match) use ($pathParams, $template): string {
                $name = $match[1];
                if (!array_key_exists($name, $pathParams)) {
                    throw new HyperSwitchApiException(
                        sprintf('Missing path parameter "%s" for template "%s".', $name, $template)
                    );
                }
                return rawurlencode((string) $pathParams[$name]);
            },
            $template
        );

        if ($path === null) {
            throw new HyperSwitchApiException(sprintf('Failed to bind path template "%s".', $template));
        }

        if ($queryParams !== []) {
            $path .= '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
        }

        return $path;
    }
}
