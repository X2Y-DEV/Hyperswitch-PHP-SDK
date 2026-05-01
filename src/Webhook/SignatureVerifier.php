<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Webhook;

use X2Y\HyperswitchClient\Exception\InvalidSignatureException;

/**
 * Verifies the HMAC signature Hyperswitch attaches to outbound webhook
 * deliveries.
 *
 * Hyperswitch signs the raw HTTP body (not the headers, not a canonical
 * representation) using HMAC with the merchant's "Payment Response Hash
 * Key" — visible as `payment_response_hash_key` in Hyperswitch's merchant
 * dashboard. By default the algorithm is SHA-512; merchants can also
 * configure SHA-256.
 *
 * The verifier accepts either algorithm — pass the desired algorithm at
 * construction time. The signature itself is the lowercase hex digest of
 * the HMAC; comparison is constant-time via `hash_equals`.
 *
 * Construct one verifier per merchant (per hash key) and reuse — the
 * class is stateless beyond the key/algorithm.
 */
final class SignatureVerifier
{
    public const ALGO_SHA512 = 'sha512';
    public const ALGO_SHA256 = 'sha256';

    public function __construct(
        private readonly string $hashKey,
        private readonly string $algorithm = self::ALGO_SHA512,
    ) {
        if ($hashKey === '') {
            throw new InvalidSignatureException('Webhook hash key must not be empty.');
        }
        if ($algorithm !== self::ALGO_SHA512 && $algorithm !== self::ALGO_SHA256) {
            throw new InvalidSignatureException(
                'Unsupported HMAC algorithm: ' . $algorithm . '. Use ALGO_SHA512 or ALGO_SHA256.'
            );
        }
    }

    /**
     * Constant-time verification of a signature against a body.
     *
     * @throws InvalidSignatureException when the signature does not match.
     */
    public function verify(string $body, string $providedSignature): void
    {
        if ($providedSignature === '') {
            throw new InvalidSignatureException('Webhook signature header is missing or empty.');
        }

        $computed = hash_hmac($this->algorithm, $body, $this->hashKey);

        if (!hash_equals($computed, $providedSignature)) {
            throw new InvalidSignatureException('Webhook signature does not match.');
        }
    }

    /**
     * Boolean form for callers that prefer not to catch exceptions.
     * Internally still constant-time.
     */
    public function isValid(string $body, string $providedSignature): bool
    {
        try {
            $this->verify($body, $providedSignature);
            return true;
        } catch (InvalidSignatureException) {
            return false;
        }
    }

    /**
     * Compute the signature for a body — useful for tests and for
     * outbound webhook fan-out (e.g. relaying events from X2Y Cloud
     * to merchants).
     */
    public function sign(string $body): string
    {
        return hash_hmac($this->algorithm, $body, $this->hashKey);
    }

    public function algorithm(): string
    {
        return $this->algorithm;
    }
}
