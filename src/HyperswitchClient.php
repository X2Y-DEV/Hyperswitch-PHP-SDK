<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use X2Y\HyperswitchClient\Auth\AuthContextResolverInterface;
use X2Y\HyperswitchClient\Resource\ApiKeysResource;
use X2Y\HyperswitchClient\Resource\BusinessProfileResource;
use X2Y\HyperswitchClient\Resource\ConnectorsResource;
use X2Y\HyperswitchClient\Resource\CustomersResource;
use X2Y\HyperswitchClient\Resource\MerchantAccountsResource;
use X2Y\HyperswitchClient\Resource\PaymentLinksResource;
use X2Y\HyperswitchClient\Resource\PaymentMethodSessionsResource;
use X2Y\HyperswitchClient\Resource\PaymentMethodsResource;
use X2Y\HyperswitchClient\Resource\PaymentsResource;
use X2Y\HyperswitchClient\Resource\RefundsResource;
use X2Y\HyperswitchClient\Resource\SubscriptionsResource;
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
    private ?RefundsResource $refunds = null;
    private ?CustomersResource $customers = null;
    private ?PaymentLinksResource $paymentLinks = null;
    private ?ApiKeysResource $apiKeys = null;
    private ?BusinessProfileResource $businessProfile = null;
    private ?MerchantAccountsResource $merchantAccounts = null;
    private ?ConnectorsResource $connectors = null;
    private ?SubscriptionsResource $subscriptions = null;
    private ?PaymentMethodsResource $paymentMethods = null;
    private ?PaymentMethodSessionsResource $paymentMethodSessions = null;

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

    public function refunds(): RefundsResource
    {
        return $this->refunds ??= new RefundsResource($this->apiClient);
    }

    public function customers(): CustomersResource
    {
        return $this->customers ??= new CustomersResource($this->apiClient);
    }

    public function paymentLinks(): PaymentLinksResource
    {
        return $this->paymentLinks ??= new PaymentLinksResource($this->apiClient);
    }

    public function apiKeys(): ApiKeysResource
    {
        return $this->apiKeys ??= new ApiKeysResource($this->apiClient);
    }

    public function businessProfile(): BusinessProfileResource
    {
        return $this->businessProfile ??= new BusinessProfileResource($this->apiClient);
    }

    public function merchantAccounts(): MerchantAccountsResource
    {
        return $this->merchantAccounts ??= new MerchantAccountsResource($this->apiClient);
    }

    public function connectors(): ConnectorsResource
    {
        return $this->connectors ??= new ConnectorsResource($this->apiClient);
    }

    public function subscriptions(): SubscriptionsResource
    {
        return $this->subscriptions ??= new SubscriptionsResource($this->apiClient);
    }

    public function paymentMethods(): PaymentMethodsResource
    {
        return $this->paymentMethods ??= new PaymentMethodsResource($this->apiClient);
    }

    public function paymentMethodSessions(): PaymentMethodSessionsResource
    {
        return $this->paymentMethodSessions ??= new PaymentMethodSessionsResource($this->apiClient);
    }
}
