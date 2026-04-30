<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Transport;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use X2Y\HyperswitchClient\Auth\AuthContextResolverInterface;
use X2Y\HyperswitchClient\Auth\AuthTier;
use X2Y\HyperswitchClient\Dto\AbstractRequestDto;
use X2Y\HyperswitchClient\Dto\AbstractResponseDto;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;
use X2Y\HyperswitchClient\Exception\TransportException;
use X2Y\HyperswitchClient\Internal\Json;
use X2Y\HyperswitchClient\Transport\Middleware\ClientMiddlewareInterface;

final class ApiClient implements ApiClientInterface
{
    /**
     * @param array<string, AuthContextResolverInterface> $authResolvers keyed by AuthTier->value
     * @param list<ClientMiddlewareInterface> $middleware
     */
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly string $baseUrl,
        private readonly array $authResolvers,
        private readonly array $middleware = [],
    ) {
    }

    public function execute(
        Endpoint $endpoint,
        ?AbstractRequestDto $request = null,
        array $pathParams = [],
        array $queryParams = [],
        array $extraHeaders = [],
    ): AbstractResponseDto|array|null {
        $path = PathBinder::bind($endpoint->pathTemplate, $pathParams, $queryParams);
        $url  = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');

        $body = $request !== null ? Json::encode($request->toArray()) : null;

        $headers = array_merge(
            ['Accept' => 'application/json'],
            $body !== null ? ['Content-Type' => 'application/json'] : [],
            $this->resolveAuthHeaders($endpoint->authTier),
            $extraHeaders,
        );

        $psrRequest = $this->requestFactory->createRequest($endpoint->method, $url);
        foreach ($headers as $name => $value) {
            $psrRequest = $psrRequest->withHeader($name, $value);
        }
        if ($body !== null) {
            $psrRequest = $psrRequest->withBody($this->streamFactory->createStream($body));
        }

        // Run middleware as a left-fold: each wraps the next layer.
        $dispatch = function (\Psr\Http\Message\RequestInterface $req): ResponseInterface {
            try {
                return $this->httpClient->sendRequest($req);
            } catch (\Psr\Http\Client\ClientExceptionInterface $e) {
                throw new TransportException(
                    'Hyperswitch transport failure: ' . $e->getMessage(),
                    0,
                    $e,
                );
            }
        };
        foreach (array_reverse($this->middleware) as $mw) {
            $next     = $dispatch;
            $dispatch = static fn (\Psr\Http\Message\RequestInterface $req): ResponseInterface
                => $mw->process($req, $next);
        }

        $response = $dispatch($psrRequest);
        $status   = $response->getStatusCode();
        $raw      = (string) $response->getBody();

        // Errors and typed responses must be JSON objects; only the
        // "no responseClass" path (list endpoints) tolerates JSON arrays.
        // Some Hyperswitch error responses arrive as plain text rather than
        // JSON — fall back to raw-body extraction so the typed status
        // exception still fires with a useful message.
        if ($status >= 400) {
            $errorPayload = [];
            if ($raw !== '') {
                try {
                    $errorPayload = Json::decode($raw);
                } catch (HyperswitchApiException) {
                    $errorPayload = ['message' => $raw];
                }
            }
            $this->throwForStatus($status, $errorPayload);
        }

        if ($endpoint->responseClass === null) {
            $any = $raw === '' ? [] : Json::decodeAny($raw);
            return $any === [] ? null : $any;
        }

        $decoded = $raw === '' ? [] : Json::decode($raw);

        /** @var class-string<AbstractResponseDto> $responseClass */
        $responseClass = $endpoint->responseClass;
        return $responseClass::fromArray($decoded);
    }

    /**
     * @return array<string, string>
     */
    private function resolveAuthHeaders(AuthTier $tier): array
    {
        $resolver = $this->authResolvers[$tier->value] ?? null;
        if ($resolver === null) {
            throw new HyperswitchApiException(
                sprintf('No AuthContextResolver registered for tier "%s".', $tier->value)
            );
        }
        return $resolver->headers();
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function throwForStatus(int $status, array $payload): never
    {
        $message = $this->extractMessage($payload, $status);
        throw match (true) {
            $status === 401, $status === 403
                => new \X2Y\HyperswitchClient\Exception\AuthenticationException($message, 0, null, $status, $payload),
            $status === 404
                => new \X2Y\HyperswitchClient\Exception\NotFoundException($message, 0, null, $status, $payload),
            $status === 400, $status === 422
                => new \X2Y\HyperswitchClient\Exception\ValidationException($message, 0, null, $status, $payload),
            $status === 429
                => new \X2Y\HyperswitchClient\Exception\RateLimitedException($message, 0, null, $status, $payload),
            $status >= 500
                => new \X2Y\HyperswitchClient\Exception\TransientException($message, 0, null, $status, $payload),
            default
                => new HyperswitchApiException($message, 0, null, $status, $payload),
        };
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractMessage(array $payload, int $status): string
    {
        // Hyperswitch uses three different error shapes depending on endpoint:
        //   1) { "error": { "message": "...", "code": "..." } }
        //   2) { "error_message": "..." }
        //   3) { "type": "...", "code": "...", "message": "..." }   (flat — IR_xx errors)
        $error = $payload['error'] ?? null;
        if (is_array($error)) {
            $message = $error['message'] ?? null;
            if (is_string($message) && $message !== '') {
                return $message;
            }
        }
        $message = $payload['error_message'] ?? null;
        if (is_string($message) && $message !== '') {
            return $message;
        }
        // Flat-shape fallback. Include the IR code if present for context.
        $message = $payload['message'] ?? null;
        if (is_string($message) && $message !== '') {
            $code = $payload['code'] ?? null;
            return is_string($code) && $code !== ''
                ? sprintf('%s [%s]', $message, $code)
                : $message;
        }
        return sprintf('Hyperswitch API error (HTTP %d).', $status);
    }
}
