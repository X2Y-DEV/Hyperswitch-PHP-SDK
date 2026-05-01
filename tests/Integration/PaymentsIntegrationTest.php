<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Tests\Integration;

use X2Y\HyperswitchClient\Dto\Common\PaginatedList;
use X2Y\HyperswitchClient\Dto\Payment\Request\CancelPaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\ListPaymentsRequest;
use X2Y\HyperswitchClient\Dto\Payment\Request\UpdatePaymentRequest;
use X2Y\HyperswitchClient\Dto\Payment\Response\PaymentResponse;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\Enum\IntentStatus;
use X2Y\HyperswitchClient\Exception\HyperswitchExceptionInterface;

final class PaymentsIntegrationTest extends AbstractIntegrationTestCase
{
    public function testCreateRetrieveUpdateCancel(): void
    {
        // 1. Create — no payment method attached, ends up in REQUIRES_PAYMENT_METHOD.
        $created = $this->client->payments()->create(new CreatePaymentRequest(
            amount:      4500,
            currency:    Currency::USD,
            description: 'integration ' . $this->prefix,
        ));
        self::assertNotEmpty($created->paymentId);
        self::assertSame(IntentStatus::REQUIRES_PAYMENT_METHOD, $created->status);
        self::assertSame(4500, $created->amount);
        self::assertSame(Currency::USD, $created->currency);
        self::assertNotEmpty($created->clientSecret);

        $paymentId = $created->paymentId;

        // Best-effort cleanup: cancel if the test fails partway.
        $this->onTearDown(function () use ($paymentId): void {
            try {
                $this->client->payments()->cancel($paymentId);
            } catch (HyperswitchExceptionInterface) {
                // already cancelled by the test or already terminal
            }
        });

        // 2. Update amount before confirmation
        $updated = $this->client->payments()->update($paymentId, new UpdatePaymentRequest(
            amount:      6000,
            description: 'integration updated',
        ));
        self::assertSame(6000, $updated->amount);

        // 3. Retrieve with force_sync=true (exercises queryParams path)
        $got = $this->client->payments()->retrieve($paymentId, ['force_sync' => 'true']);
        self::assertSame(6000, $got->amount);

        // 4. Cancel
        $cancelled = $this->client->payments()->cancel($paymentId, new CancelPaymentRequest(
            cancellationReason: 'integration_test_cleanup',
        ));
        self::assertSame(IntentStatus::CANCELLED, $cancelled->status);
    }

    public function testListReturnsTypedPaginatedList(): void
    {
        $list = $this->client->payments()->list(new ListPaymentsRequest(limit: 5));
        self::assertInstanceOf(PaginatedList::class, $list);

        // Type system check: each item is a PaymentResponse.
        foreach ($list->items() as $payment) {
            self::assertInstanceOf(PaymentResponse::class, $payment);
            self::assertNotEmpty($payment->paymentId);
        }
    }
}
