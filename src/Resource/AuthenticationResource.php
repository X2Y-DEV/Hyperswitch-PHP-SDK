<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Resource;

use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationActionRequest;
use X2Y\HyperSwitchClient\Dto\Authentication\Request\AuthenticationCreateRequest;
use X2Y\HyperSwitchClient\Dto\Authentication\Response\AuthenticationResponse;
use X2Y\HyperSwitchClient\Endpoint\AuthenticationEndpoints;
use X2Y\HyperSwitchClient\Exception\HyperSwitchApiException;
use X2Y\HyperSwitchClient\Transport\Endpoint;

/**
 * Typed facade over the HyperSwitch Authentication group (external 3DS).
 *
 * @openapi             tag: Authentication
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class AuthenticationResource extends AbstractResource
{
    /** @openapi "Create an Authentication" */
    public function create(AuthenticationCreateRequest $request): AuthenticationResponse
    {
        $response = $this->execute(AuthenticationEndpoints::create(), request: $request);
        return $this->asAuthentication($response, 'create');
    }

    /** @openapi "Authenticate an Authentication" */
    public function authenticate(string $authenticationId, AuthenticationActionRequest $request): AuthenticationResponse
    {
        $response = $this->execute(
            AuthenticationEndpoints::authenticate(),
            request:    $request,
            pathParams: ['id' => $authenticationId],
        );
        return $this->asAuthentication($response, 'authenticate');
    }

    /** @openapi "Check Authentication Eligibility" */
    public function eligibility(string $authenticationId, AuthenticationActionRequest $request): AuthenticationResponse
    {
        $response = $this->execute(
            AuthenticationEndpoints::eligibility(),
            request:    $request,
            pathParams: ['id' => $authenticationId],
        );
        return $this->asAuthentication($response, 'eligibility');
    }

    /** @openapi "Sync an Authentication" */
    public function sync(string $authenticationId, AuthenticationActionRequest $request): AuthenticationResponse
    {
        $response = $this->execute(
            AuthenticationEndpoints::sync(),
            request:    $request,
            pathParams: ['id' => $authenticationId],
        );
        return $this->asAuthentication($response, 'sync');
    }

    /**
     * @openapi "Retrieve Eligibility Check data for an Authentication"
     *
     * @return array<int|string, mixed>
     */
    public function eligibilityCheckRetrieve(string $authenticationId, AuthenticationActionRequest $request): array
    {
        return $this->raw(
            AuthenticationEndpoints::eligibilityCheckRetrieve(),
            'eligibilityCheckRetrieve',
            $authenticationId,
            $request,
        );
    }

    /**
     * @openapi "Submit Eligibility for an Authentication"
     *
     * @return array<int|string, mixed>
     */
    public function eligibilityCheckSubmit(string $authenticationId, AuthenticationActionRequest $request): array
    {
        return $this->raw(
            AuthenticationEndpoints::eligibilityCheckSubmit(),
            'eligibilityCheckSubmit',
            $authenticationId,
            $request,
        );
    }

    /**
     * @openapi "Enable Authentication Authn Methods Token"
     *
     * @return array<int|string, mixed>
     */
    public function sessionToken(string $authenticationId, AuthenticationActionRequest $request): array
    {
        return $this->raw(
            AuthenticationEndpoints::sessionToken(),
            'sessionToken',
            $authenticationId,
            $request,
        );
    }

    /** @openapi "Redirect an Authentication" */
    public function redirect(string $authenticationId, AuthenticationActionRequest $request): void
    {
        $this->execute(
            AuthenticationEndpoints::redirect(),
            request:    $request,
            pathParams: ['id' => $authenticationId],
        );
    }

    private function asAuthentication(mixed $response, string $context): AuthenticationResponse
    {
        if (!$response instanceof AuthenticationResponse) {
            throw new HyperSwitchApiException("Unexpected response type from Authentication::{$context}.");
        }
        return $response;
    }

    /**
     * @return array<int|string, mixed>
     */
    private function raw(
        Endpoint $endpoint,
        string $context,
        string $authenticationId,
        AuthenticationActionRequest $request,
    ): array {
        $response = $this->execute($endpoint, request: $request, pathParams: ['id' => $authenticationId]);
        if ($response !== null && !is_array($response)) {
            throw new HyperSwitchApiException("Unexpected response type from Authentication::{$context}.");
        }
        /** @var array<int|string, mixed> $raw */
        $raw = $response ?? [];
        return $raw;
    }
}
