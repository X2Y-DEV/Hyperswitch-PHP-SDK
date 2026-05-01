<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Unit\Dto\Customer\Response;

use PHPUnit\Framework\TestCase;
use X2Y\HyperswitchClient\Dto\Customer\Response\CustomerDeleteResponse;
use X2Y\HyperswitchClient\Dto\Customer\Response\CustomerResponse;
use X2Y\HyperswitchClient\Dto\Customer\Response\EphemeralKeyResponse;

final class CustomerResponsesTest extends TestCase
{
    public function testCustomerResponseFullPayload(): void
    {
        $c = CustomerResponse::fromArray([
            'customer_id'        => 'cus_42',
            'email'              => 'a@b.com',
            'name'               => 'Smoke Test',
            'phone'              => '5551234567',
            'phone_country_code' => '+1',
            'description'        => 'Test',
            'address'            => ['line1' => '1 Main', 'city' => 'SF', 'country' => 'US'],
            'metadata'           => ['tier' => 'gold'],
            'created_at'         => '2026-04-30T10:00:00Z',
            'default_payment_method_id' => 'pm_42',
        ]);
        self::assertSame('cus_42', $c->customerId);
        self::assertSame('a@b.com', $c->email);
        self::assertNotNull($c->address);
        self::assertSame('SF', $c->address->city);
        self::assertSame('gold', $c->metadata['tier'] ?? null);
        self::assertNotNull($c->createdAt);
        self::assertSame('pm_42', $c->defaultPaymentMethodId);
    }

    public function testCustomerResponseNoOptionals(): void
    {
        $c = CustomerResponse::fromArray(['customer_id' => 'cus_x']);
        self::assertSame('cus_x', $c->customerId);
        self::assertNull($c->email);
        self::assertNull($c->address);
    }

    public function testEphemeralKeyResponseHandlesUnixEpoch(): void
    {
        // The reason DateTimeParser exists — Hyperswitch returns Unix int timestamps here.
        $e = EphemeralKeyResponse::fromArray([
            'customer_id' => 'cus_42',
            'id'          => 'eki_xyz',
            'secret'      => 'epk_secret_blob',
            'created_at'  => 1777581234,
            'expires'     => 1777584834,
        ]);
        self::assertSame('eki_xyz', $e->id);
        self::assertNotNull($e->createdAt);
        self::assertSame(1777581234, $e->createdAt->getTimestamp());
        self::assertNotNull($e->expires);
        self::assertSame(1777584834, $e->expires->getTimestamp());
    }

    public function testEphemeralKeyResponseIsoStringStillWorks(): void
    {
        $e = EphemeralKeyResponse::fromArray([
            'customer_id' => 'cus_42',
            'id'          => 'eki_xyz',
            'secret'      => 'epk_secret_blob',
            'created_at'  => '2026-04-30T10:00:00Z',
        ]);
        self::assertNotNull($e->createdAt);
    }

    public function testCustomerDeleteResponse(): void
    {
        $d = CustomerDeleteResponse::fromArray([
            'customer_id'             => 'cus_42',
            'customer_deleted'        => true,
            'address_deleted'         => true,
            'payment_methods_deleted' => false,
        ]);
        self::assertSame('cus_42', $d->customerId);
        self::assertTrue($d->customerDeleted);
        self::assertTrue($d->addressDeleted);
        self::assertFalse($d->paymentMethodsDeleted);
    }

    public function testCustomerDeleteResponseDefaults(): void
    {
        $d = CustomerDeleteResponse::fromArray(['customer_id' => 'cus_42']);
        self::assertFalse($d->customerDeleted);  // default
        self::assertNull($d->addressDeleted);
    }
}
