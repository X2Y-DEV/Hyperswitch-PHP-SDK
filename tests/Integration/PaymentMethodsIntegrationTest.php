<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Tests\Integration;

use X2Y\HyperSwitchClient\Exception\HyperSwitchExceptionInterface;
use X2Y\HyperSwitchClient\Exception\NotFoundException;
use X2Y\HyperSwitchClient\Exception\ValidationException;

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
     * V1 auth scheme is now wired up (Authorization: api-key=<key>). With
     * the right scheme + a profile id, retrieve of a non-existent payment
     * method returns 404 typed as NotFoundException.
     *
     * Without the profile id, HyperSwitch returns 422 "Missing header key:
     * X-Profile-Id" — also acceptable; the V1 auth itself is no longer the
     * blocker. Set HYPERSWITCH_SANDBOX_PROFILE_ID to exercise the 404 path.
     */
    public function testRetrieveWithV1AuthGetsPastTheAuthGate(): void
    {
        $resource = $this->profileId !== null
            ? $this->client->paymentMethods()->withProfileId($this->profileId)
            : $this->client->paymentMethods();

        try {
            $resource->retrieve($this->uniqueId('pm'));
            self::fail('expected an authentication / not-found exception');
        } catch (NotFoundException $e) {
            // Happy path: V1 auth + profile id authenticate; PM doesn't exist; 404.
            self::assertSame(404, $e->httpStatus);
        } catch (ValidationException $e) {
            // Profile id missing — but the V1 auth itself worked: error has
            // shifted from "Missing required param: Authorization" to
            // "Missing header key: X-Profile-Id".
            self::assertStringContainsString('X-Profile-Id', $e->getMessage());
        } catch (HyperSwitchExceptionInterface $e) {
            self::fail('unexpected SDK exception: ' . get_class($e) . ' — ' . $e->getMessage());
        }
    }
}
