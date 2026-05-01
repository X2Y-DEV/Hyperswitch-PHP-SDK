<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Integration;

use X2Y\HyperswitchClient\Exception\HyperswitchExceptionInterface;
use X2Y\HyperswitchClient\Exception\NotFoundException;
use X2Y\HyperswitchClient\Exception\ValidationException;

final class PaymentMethodsIntegrationTest extends AbstractIntegrationTestCase
{
    /**
     * `listForCustomer` against a non-existent customer id returns 404 with a
     * plain-text error body — exactly the path the ApiClient plain-text
     * error fallback exists for. If this test ever stops returning a typed
     * NotFoundException, the fallback regressed.
     */
    public function testListForNonExistentCustomerReturnsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->paymentMethods()->listForCustomer($this->uniqueId('cus_nope'));
    }

    /**
     * `GET /v1/payment-methods/{id}` rejects merchant-key authentication with
     * "Missing required param: Authorization" — this V1 endpoint expects a
     * Bearer Authorization header rather than the `api-key` header that the
     * MerchantKeyAuthResolver sets. The SDK does not yet support per-Resource
     * Authorization-header injection; that's planned for Phase 4d.
     *
     * This test pins the *current* behavior. When 4d ships per-Resource
     * `extraHeaders` and the test starts catching NotFoundException, update
     * the assertion accordingly — that's the signal the auth gap was closed.
     */
    public function testRetrieveCurrentlyRejectsMerchantAuth(): void
    {
        try {
            $this->client->paymentMethods()->retrieve($this->uniqueId('pm'));
            self::fail('expected an authentication / not-found exception');
        } catch (ValidationException $e) {
            // Current behavior — V1 endpoint demands Authorization header.
            self::assertStringContainsString('Authorization', $e->getMessage());
        } catch (NotFoundException) {
            // Future behavior — once 4d adds per-Resource extraHeaders the
            // request authenticates correctly and Hyperswitch returns 404.
            self::assertTrue(true);
        } catch (HyperswitchExceptionInterface $e) {
            self::fail('unexpected SDK exception: ' . get_class($e) . ' — ' . $e->getMessage());
        }
    }
}
