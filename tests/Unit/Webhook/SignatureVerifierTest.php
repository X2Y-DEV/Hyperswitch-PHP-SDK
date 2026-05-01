<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Unit\Webhook;

use PHPUnit\Framework\TestCase;
use X2Y\HyperSwitchClient\Exception\InvalidSignatureException;
use X2Y\HyperSwitchClient\Webhook\SignatureVerifier;

final class SignatureVerifierTest extends TestCase
{
    private const KEY  = 'jP3uE7c9UhXBg8uGWlpuexqNtFXi2y10';
    private const BODY = '{"event_type":"payment_succeeded","content":{"object":{"payment_id":"pay_abc"}}}';

    public function testRoundTripSha512(): void
    {
        $v   = new SignatureVerifier(self::KEY, SignatureVerifier::ALGO_SHA512);
        $sig = $v->sign(self::BODY);
        $v->verify(self::BODY, $sig); // no throw == pass
        self::assertTrue($v->isValid(self::BODY, $sig));
    }

    public function testRoundTripSha256(): void
    {
        $v   = new SignatureVerifier(self::KEY, SignatureVerifier::ALGO_SHA256);
        $sig = $v->sign(self::BODY);
        $v->verify(self::BODY, $sig);
        self::assertTrue($v->isValid(self::BODY, $sig));
    }

    public function testInvalidSignatureRejected(): void
    {
        $v = new SignatureVerifier(self::KEY);
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('does not match');
        $v->verify(self::BODY, str_repeat('0', 128));
    }

    public function testEmptySignatureRejected(): void
    {
        $v = new SignatureVerifier(self::KEY);
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('missing or empty');
        $v->verify(self::BODY, '');
    }

    public function testEmptyKeyRejectedAtConstruction(): void
    {
        $this->expectException(InvalidSignatureException::class);
        new SignatureVerifier('');
    }

    public function testUnsupportedAlgorithmRejectedAtConstruction(): void
    {
        $this->expectException(InvalidSignatureException::class);
        new SignatureVerifier(self::KEY, 'md5');
    }

    public function testWrongAlgorithmDoesNotMatch(): void
    {
        $signer   = new SignatureVerifier(self::KEY, SignatureVerifier::ALGO_SHA512);
        $verifier = new SignatureVerifier(self::KEY, SignatureVerifier::ALGO_SHA256);
        self::assertFalse($verifier->isValid(self::BODY, $signer->sign(self::BODY)));
    }

    public function testIsValidReturnsBoolean(): void
    {
        $v   = new SignatureVerifier(self::KEY);
        $sig = $v->sign(self::BODY);
        self::assertTrue($v->isValid(self::BODY, $sig));
        self::assertFalse($v->isValid(self::BODY, 'wrong'));
    }
}
