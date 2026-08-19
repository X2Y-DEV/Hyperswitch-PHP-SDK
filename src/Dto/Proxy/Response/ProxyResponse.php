<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Proxy\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Response from POST /v1/proxy. HyperSwitch surfaces the upstream response
 * as a passthrough — most of the body is connector-shaped and lives in
 * `extras`.
 *
 * @endpoint            POST /v1/proxy
 * @hyperswitch-source  2026-04-30
 */
final class ProxyResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed>|null $body
     * @param array<string, string>|null $headers
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?int $statusCode = null,
        public readonly ?array $headers = null,
        public readonly ?array $body = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['status_code', 'headers', 'body'];

        $headers = $data['headers'] ?? null;
        $cleanedHeaders = null;
        if (is_array($headers)) {
            $cleanedHeaders = [];
            foreach ($headers as $k => $v) {
                if (is_string($k) && (is_string($v) || is_numeric($v))) {
                    $cleanedHeaders[$k] = (string) $v;
                }
            }
        }

        $body = $data['body'] ?? null;

        return new self(
            statusCode: isset($data['status_code']) ? (int) $data['status_code'] : null,
            headers:    $cleanedHeaders,
            body:       is_array($body) ? $body : null,
            extras:     array_diff_key($data, array_flip($known)),
        );
    }
}
