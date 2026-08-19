<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use X2Y\HyperSwitchClient\Auth\AuthContextResolverInterface;
use X2Y\HyperSwitchClient\Resource\ApiKeysResource;
use X2Y\HyperSwitchClient\Resource\BusinessProfileResource;
use X2Y\HyperSwitchClient\Resource\ConnectorsResource;
use X2Y\HyperSwitchClient\Resource\CustomersResource;
use X2Y\HyperSwitchClient\Resource\DisputesResource;
use X2Y\HyperSwitchClient\Resource\MandatesResource;
use X2Y\HyperSwitchClient\Resource\MerchantAccountsResource;
use X2Y\HyperSwitchClient\Resource\PaymentLinksResource;
use X2Y\HyperSwitchClient\Resource\PaymentMethodSessionsResource;
use X2Y\HyperSwitchClient\Resource\PaymentMethodsResource;
use X2Y\HyperSwitchClient\Resource\PaymentMethodsV1Resource;
use X2Y\HyperSwitchClient\Resource\PaymentsResource;
use X2Y\HyperSwitchClient\Resource\PayoutsResource;
use X2Y\HyperSwitchClient\Resource\AuthenticationResource;
use X2Y\HyperSwitchClient\Resource\PlatformResource;
use X2Y\HyperSwitchClient\Resource\PollResource;
use X2Y\HyperSwitchClient\Resource\RoutingResource;
use X2Y\HyperSwitchClient\Resource\ThreeDsDecisionResource;
use X2Y\HyperSwitchClient\Resource\ProxyResource;
use X2Y\HyperSwitchClient\Resource\RefundsResource;
use X2Y\HyperSwitchClient\Resource\RelayResource;
use X2Y\HyperSwitchClient\Resource\SubscriptionsResource;
use X2Y\HyperSwitchClient\Transport\ApiClient;
use X2Y\HyperSwitchClient\Transport\ApiClientInterface;
use X2Y\HyperSwitchClient\Transport\Middleware\ClientMiddlewareInterface;

/**
 * Top-level entry point. Construct via the named factories
 * (`sandbox()`, `production()`) or directly with a custom base URL.
 *
 * Resources are lazily instantiated on first access.
 */
final class HyperSwitchClient implements HyperSwitchClientInterface
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
    private ?PaymentMethodsV1Resource $paymentMethodsV1 = null;
    private ?PaymentMethodSessionsResource $paymentMethodSessions = null;
    private ?MandatesResource $mandates = null;
    private ?DisputesResource $disputes = null;
    private ?PayoutsResource $payouts = null;
    private ?RelayResource $relay = null;
    private ?ProxyResource $proxy = null;
    private ?PlatformResource $platform = null;
    private ?RoutingResource $routing = null;
    private ?PollResource $poll = null;
    private ?ThreeDsDecisionResource $threeDsDecision = null;
    private ?AuthenticationResource $authentication = null;

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

    /**
     * The v1 `/payment_methods` surface (distinct from paymentMethods(), which
     * wraps the v2-compat modular `/v1/payment-methods` flow).
     */
    public function paymentMethodsV1(): PaymentMethodsV1Resource
    {
        return $this->paymentMethodsV1 ??= new PaymentMethodsV1Resource($this->apiClient);
    }

    public function paymentMethodSessions(): PaymentMethodSessionsResource
    {
        return $this->paymentMethodSessions ??= new PaymentMethodSessionsResource($this->apiClient);
    }

    public function mandates(): MandatesResource
    {
        return $this->mandates ??= new MandatesResource($this->apiClient);
    }

    public function disputes(): DisputesResource
    {
        return $this->disputes ??= new DisputesResource($this->apiClient);
    }

    public function payouts(): PayoutsResource
    {
        return $this->payouts ??= new PayoutsResource($this->apiClient);
    }

    public function relay(): RelayResource
    {
        return $this->relay ??= new RelayResource($this->apiClient);
    }

    public function proxy(): ProxyResource
    {
        return $this->proxy ??= new ProxyResource($this->apiClient);
    }

    public function platform(): PlatformResource
    {
        return $this->platform ??= new PlatformResource($this->apiClient);
    }

    public function routing(): RoutingResource
    {
        return $this->routing ??= new RoutingResource($this->apiClient);
    }

    public function poll(): PollResource
    {
        return $this->poll ??= new PollResource($this->apiClient);
    }

    public function threeDsDecision(): ThreeDsDecisionResource
    {
        return $this->threeDsDecision ??= new ThreeDsDecisionResource($this->apiClient);
    }

    public function authentication(): AuthenticationResource
    {
        return $this->authentication ??= new AuthenticationResource($this->apiClient);
    }
}
