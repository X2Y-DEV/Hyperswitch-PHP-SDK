<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Transport;

use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\AbstractResponseDto;

/**
 * Value object describing one Hyperswitch endpoint.
 *
 * Resources construct Endpoints (typically via static factory methods on
 * `<Group>Endpoints` classes) and pass them to the ApiClient. The Endpoint
 * carries everything needed to dispatch and decode the call: HTTP method,
 * path template (with `{name}` placeholders), required auth tier,
 * idempotency hint, and the Request/Response DTO classes.
 */
final class Endpoint
{
    /**
     * @param class-string<AbstractRequestDto>|null $requestClass
     * @param class-string<AbstractResponseDto>|null $responseClass
     */
    public function __construct(
        public readonly string $method,
        public readonly string $pathTemplate,
        public readonly AuthTier $authTier,
        public readonly bool $idempotent = false,
        public readonly ?string $requestClass = null,
        public readonly ?string $responseClass = null,
    ) {
    }

    public function requiresIdempotencyKey(): bool
    {
        return $this->idempotent && in_array($this->method, ['POST', 'PUT', 'PATCH'], true);
    }
}
