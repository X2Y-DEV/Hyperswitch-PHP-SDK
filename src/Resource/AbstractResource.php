<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;
use X2Y\HyperswitchClient\Transport\Endpoint;

/**
 * Base class for every typed Resource facade. Provides a small
 * `extraHeaders` mechanism that subclasses can use to carry Resource-
 * scoped headers (e.g. `X-Profile-Id`) without making every method
 * accept a `headers` argument.
 *
 * Idiomatic usage on the consumer side:
 *
 *     $client->subscriptions()
 *            ->withProfileId('pro_42')
 *            ->retrieve('sub_xyz');
 *
 * `with*()` returns a new Resource instance with the merged header set,
 * leaving the original intact — safe to share a base Resource across
 * multiple business contexts.
 *
 * @phpstan-consistent-constructor   Subclasses must not override the
 *   constructor signature; `with*()` relies on `new static($client, $h)`.
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * @param array<string, string> $extraHeaders Resource-scoped headers
     *        merged into every outbound request from this Resource.
     */
    public function __construct(
        protected readonly ApiClientInterface $client,
        protected readonly array $extraHeaders = [],
    ) {
    }

    /**
     * Return a new Resource instance with the given headers merged onto the
     * existing extra-header set. Existing keys are overwritten.
     *
     * @param array<string, string> $headers
     */
    final public function withExtraHeaders(array $headers): static
    {
        /** @var static $clone */
        $clone = new static($this->client, [...$this->extraHeaders, ...$headers]);
        return $clone;
    }

    /**
     * Convenience for the most common case: setting `X-Profile-Id` on
     * Resources that need to scope every call to a business profile.
     */
    final public function withProfileId(string $profileId): static
    {
        return $this->withExtraHeaders(['X-Profile-Id' => $profileId]);
    }

    /**
     * @return array<string, string>
     */
    final public function extraHeaders(): array
    {
        return $this->extraHeaders;
    }

    /**
     * Dispatch helper. Same signature as ApiClientInterface::execute() but
     * automatically merges the Resource's `extraHeaders` into every call.
     * Per-call headers passed here win on key collision.
     *
     * @param array<string, scalar> $pathParams
     * @param array<string, scalar|array<string, scalar>> $queryParams
     * @param array<string, string> $extraHeaders
     *
     * @return AbstractResponseDto|array<int|string, mixed>|null
     */
    final protected function execute(
        Endpoint $endpoint,
        ?AbstractRequestDto $request = null,
        array $pathParams = [],
        array $queryParams = [],
        array $extraHeaders = [],
    ): AbstractResponseDto|array|null {
        return $this->client->execute(
            $endpoint,
            request:      $request,
            pathParams:   $pathParams,
            queryParams:  $queryParams,
            extraHeaders: [...$this->extraHeaders, ...$extraHeaders],
        );
    }
}
