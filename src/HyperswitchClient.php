<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use X2Y\HyperswitchClient\Auth\AuthContextResolverInterface;
use X2Y\HyperswitchClient\Resource\PaymentsResource;
use X2Y\HyperswitchClient\Transport\ApiClient;
use X2Y\HyperswitchClient\Transport\ApiClientInterface;
use X2Y\HyperswitchClient\Transport\Middleware\ClientMiddlewareInterface;

/**
 * Top-level entry point. Construct via the named factories
 * (`sandbox()`, `production()`) or directly with a custom base URL.
 *
 * Resources are lazily instantiated on first access.
 */
final class HyperswitchClient implements HyperswitchClientInterface
{
    public const SANDBOX_BASE_URL    = 'https://sandbox.hyperswitch.io';
    public const PRODUCTION_BASE_URL = 'https://api.hyperswitch.io';

    private ?PaymentsResource $payments = null;

    public function __construct(
        private readonly ApiClientInterface $apiClient,
    ) {
    }

    /**
     * @param array<AuthContextResolverInterface> $auth one or more resolvers; each registered under its tier
     * @param list<ClientMiddlewareInterface> $middleware
     */
    public static function sandbox(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        array $auth,
        array $middleware = [],
    ): self {
        return self::withBaseUrl(self::SANDBOX_BASE_URL, $httpClient, $requestFactory, $streamFactory, $auth, $middleware);
    }

    /**
     * @param array<AuthContextResolverInterface> $auth
     * @param list<ClientMiddlewareInterface> $middleware
     */
    public static function production(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        array $auth,
        array $middleware = [],
    ): self {
        return self::withBaseUrl(self::PRODUCTION_BASE_URL, $httpClient, $requestFactory, $streamFactory, $auth, $middleware);
    }

    /**
     * @param array<AuthContextResolverInterface> $auth
     * @param list<ClientMiddlewareInterface> $middleware
     */
    public static function withBaseUrl(
        string $baseUrl,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        array $auth,
        array $middleware = [],
    ): self {
        $resolversByTier = [];
        foreach ($auth as $resolver) {
            $resolversByTier[$resolver->tier()->value] = $resolver;
        }

        $apiClient = new ApiClient(
            httpClient:     $httpClient,
            requestFactory: $requestFactory,
            streamFactory:  $streamFactory,
            baseUrl:        $baseUrl,
            authResolvers:  $resolversByTier,
            middleware:     $middleware,
        );

        return new self($apiClient);
    }

    public function payments(): PaymentsResource
    {
        return $this->payments ??= new PaymentsResource($this->apiClient);
    }
}
