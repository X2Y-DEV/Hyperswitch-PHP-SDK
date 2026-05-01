<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Transport;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Exception\AuthenticationException;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Exception\NotFoundException;
use X2Y\HyperswitchClient\Exception\RateLimitedException;
use X2Y\HyperswitchClient\Exception\TransientException;
use X2Y\HyperswitchClient\Exception\TransportException;
use X2Y\HyperswitchClient\Exception\ValidationException;
use X2Y\HyperswitchClient\Transport\ApiClient;
use X2Y\HyperswitchClient\Transport\Endpoint;
use X2Y\HyperswitchClient\Transport\Middleware\ClientMiddlewareInterface;

final class ApiClientTest extends TestCase
{
    private Psr17Factory $psr17;
    /** @var array<int, RequestInterface> */
    private array $sentRequests = [];

    protected function setUp(): void
    {
        $this->psr17 = new Psr17Factory();
        $this->sentRequests = [];
    }

    /** Captures every request that goes out and returns the queued response. */
    private function clientReturning(ResponseInterface $response): ClientInterface
    {
        $captures =& $this->sentRequests;
        return new class($response, $captures) implements ClientInterface {
            /** @param array<int, RequestInterface> $captures */
            public function __construct(
                private readonly ResponseInterface $response,
                private array &$captures,
            ) {}
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->captures[] = $request;
                return $this->response;
            }
        };
    }

    private function clientThrowing(\Throwable $e): ClientInterface
    {
        return new class($e) implements ClientInterface {
            public function __construct(private readonly \Throwable $e) {}
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw $this->e;
            }
        };
    }

    /** @param array<string, MerchantKeyAuthResolver|object> $resolvers */
    private function makeClient(ClientInterface $http, array $resolvers, array $middleware = []): ApiClient
    {
        return new ApiClient(
            httpClient:     $http,
            requestFactory: $this->psr17,
            streamFactory:  $this->psr17,
            baseUrl:        'https://example.test',
            authResolvers:  $resolvers,
            middleware:     $middleware,
        );
    }

    private function paymentEndpoint(): Endpoint
    {
        return new Endpoint(
            method:        'POST',
            pathTemplate:  '/payments',
            authTier:      AuthTier::MERCHANT,
            idempotent:    true,
            requestClass:  CreatePaymentRequest::class,
            responseClass: PaymentResponse::class,
        );
    }

    public function testSuccessfulPostDispatchesAndReturnsTypedResponse(): void
    {
        $http = $this->clientReturning(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'payment_id' => 'pay_abc',
            'status'     => 'requires_payment_method',
            'amount'     => 4500,
            'currency'   => 'USD',
        ], JSON_THROW_ON_ERROR)));

        $client = $this->makeClient($http, [
            'merchant' => new MerchantKeyAuthResolver('snd_test'),
        ]);

        $response = $client->execute(
            $this->paymentEndpoint(),
            request: new CreatePaymentRequest(amount: 4500, currency: Currency::USD),
        );

        self::assertInstanceOf(PaymentResponse::class, $response);
        self::assertSame('pay_abc', $response->paymentId);

        // Validate the outbound HTTP request was assembled correctly.
        self::assertCount(1, $this->sentRequests);
        $sent = $this->sentRequests[0];
        self::assertSame('POST', $sent->getMethod());
        self::assertSame('https://example.test/payments', (string) $sent->getUri());
        self::assertSame('snd_test', $sent->getHeaderLine('api-key'));
        self::assertSame('application/json', $sent->getHeaderLine('Content-Type'));
        $body = json_decode((string) $sent->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(4500, $body['amount']);
        self::assertSame('USD', $body['currency']);
    }

    public function testGetWithPathParamsAndQueryString(): void
    {
        $http = $this->clientReturning(new Response(200, [], json_encode([
            'payment_id' => 'pay_x',
            'status'     => 'succeeded',
            'amount'     => 1,
            'currency'   => 'USD',
        ], JSON_THROW_ON_ERROR)));

        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd_test')]);

        $client->execute(
            new Endpoint(
                method:        'GET',
                pathTemplate:  '/payments/{id}',
                authTier:      AuthTier::MERCHANT,
                idempotent:    false,
                responseClass: PaymentResponse::class,
            ),
            pathParams:  ['id' => 'pay_x'],
            queryParams: ['force_sync' => 'true'],
        );

        $sent = $this->sentRequests[0];
        self::assertSame('GET', $sent->getMethod());
        self::assertSame('https://example.test/payments/pay_x?force_sync=true', (string) $sent->getUri());
        self::assertSame('', (string) $sent->getBody());
    }

    public function testStatusMappingTo401IsAuthenticationException(): void
    {
        $http = $this->clientReturning(new Response(401, [], '{"error":{"message":"Bad key"}}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('bad')]);

        try {
            $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
            self::fail('expected AuthenticationException');
        } catch (AuthenticationException $e) {
            self::assertSame(401, $e->httpStatus);
            self::assertSame('Bad key', $e->getMessage());
        }
    }

    public function testStatusMappingTo404IsNotFoundException(): void
    {
        $http = $this->clientReturning(new Response(404, [], '{"error":{"message":"Not found"}}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $this->expectException(NotFoundException::class);
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
    }

    public function testStatusMappingTo422IsValidationException(): void
    {
        $http = $this->clientReturning(new Response(422, [], '{"error":{"message":"bad data"}}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $this->expectException(ValidationException::class);
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
    }

    public function testStatusMappingTo429IsRateLimited(): void
    {
        $http = $this->clientReturning(new Response(429, [], '{"error":{"message":"slow down"}}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $this->expectException(RateLimitedException::class);
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
    }

    public function testStatusMappingTo5xxIsTransient(): void
    {
        $http = $this->clientReturning(new Response(503, [], '{"error_message":"unavailable"}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        try {
            $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
            self::fail('expected TransientException');
        } catch (TransientException $e) {
            self::assertSame('unavailable', $e->getMessage());
        }
    }

    public function testFlatErrorShapeMessageExtraction(): void
    {
        // Flat-shape: {"type":"...", "code":"IR_04", "message":"..."}
        $http = $this->clientReturning(new Response(400, [], '{"type":"invalid_request_error","code":"IR_04","message":"Missing required param: X-Profile-Id"}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        try {
            $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
            self::fail('expected ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString('Missing required param: X-Profile-Id', $e->getMessage());
            self::assertStringContainsString('IR_04', $e->getMessage());
        }
    }

    public function testPlainTextErrorBodyFallback(): void
    {
        $http = $this->clientReturning(new Response(404, [], 'the minimum required length for this field is 32'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        try {
            $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
            self::fail('expected NotFoundException');
        } catch (NotFoundException $e) {
            self::assertSame('the minimum required length for this field is 32', $e->getMessage());
        }
    }

    public function testTransportFailureMappedToTransportException(): void
    {
        $psrEx = new class extends \Exception implements ClientExceptionInterface {};
        $http = $this->clientThrowing($psrEx);
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $this->expectException(TransportException::class);
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
    }

    public function testMissingAuthResolverForTierThrows(): void
    {
        $http = $this->clientReturning(new Response(200, [], '{}'));
        $client = $this->makeClient($http, []); // no resolvers registered
        $this->expectException(HyperswitchApiException::class);
        $this->expectExceptionMessage('No AuthContextResolver registered for tier "merchant"');
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
    }

    public function testNoResponseClassReturnsRawDecodedArray(): void
    {
        $http = $this->clientReturning(new Response(200, [], '[{"id":"a"},{"id":"b"}]'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);

        $result = $client->execute(new Endpoint(
            method:        'GET',
            pathTemplate:  '/things',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        ));
        self::assertIsArray($result);
        self::assertCount(2, $result);
    }

    public function testEmptyResponseBodyReturnsNullForListEndpoint(): void
    {
        $http = $this->clientReturning(new Response(204, [], ''));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $result = $client->execute(new Endpoint(
            method:        'GET',
            pathTemplate:  '/things',
            authTier:      AuthTier::MERCHANT,
            idempotent:    false,
            requestClass:  null,
            responseClass: null,
        ));
        self::assertNull($result);
    }

    public function testListResponseWhenResponseClassDeclaredThrows(): void
    {
        $http = $this->clientReturning(new Response(200, [], '[1, 2, 3]'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $this->expectException(HyperswitchApiException::class);
        $this->expectExceptionMessage('returned a JSON array');
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
    }

    public function testMiddlewareIsInvokedInOrderAndCanMutateRequests(): void
    {
        $tagger = new class implements ClientMiddlewareInterface {
            public function process(RequestInterface $request, callable $next): ResponseInterface
            {
                return $next($request->withHeader('X-Trace-Id', 'tr_42'));
            }
        };
        $http = $this->clientReturning(new Response(200, [], '{"payment_id":"pay_x","status":"succeeded","amount":1,"currency":"USD"}'));
        $client = $this->makeClient(
            $http,
            ['merchant' => new MerchantKeyAuthResolver('snd')],
            [$tagger],
        );
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
        self::assertSame('tr_42', $this->sentRequests[0]->getHeaderLine('X-Trace-Id'));
    }

    public function testMiddlewareOrderIsLeftToRight(): void
    {
        // Two middleware: first appends "A", second appends "B". Outer-first.
        $first = new class implements ClientMiddlewareInterface {
            public function process(RequestInterface $request, callable $next): ResponseInterface
            {
                return $next($request->withHeader('X-Order', $request->getHeaderLine('X-Order') . 'A'));
            }
        };
        $second = new class implements ClientMiddlewareInterface {
            public function process(RequestInterface $request, callable $next): ResponseInterface
            {
                return $next($request->withHeader('X-Order', $request->getHeaderLine('X-Order') . 'B'));
            }
        };
        $http = $this->clientReturning(new Response(200, [], '{"payment_id":"pay_x","status":"succeeded","amount":1,"currency":"USD"}'));
        $client = $this->makeClient(
            $http,
            ['merchant' => new MerchantKeyAuthResolver('snd')],
            [$first, $second],
        );
        $client->execute($this->paymentEndpoint(), request: new CreatePaymentRequest(amount: 1, currency: Currency::USD));
        // Both middleware ran, outer-first leaves "AB" by the time it reaches the http client
        self::assertSame('AB', $this->sentRequests[0]->getHeaderLine('X-Order'));
    }

    public function testExtraHeadersOverrideDefaultsAndAreSent(): void
    {
        $http = $this->clientReturning(new Response(200, [], '{"payment_id":"pay_x","status":"succeeded","amount":1,"currency":"USD"}'));
        $client = $this->makeClient($http, ['merchant' => new MerchantKeyAuthResolver('snd')]);
        $client->execute(
            $this->paymentEndpoint(),
            request:      new CreatePaymentRequest(amount: 1, currency: Currency::USD),
            extraHeaders: ['X-Profile-Id' => 'pro_42'],
        );
        self::assertSame('pro_42', $this->sentRequests[0]->getHeaderLine('X-Profile-Id'));
    }
}
