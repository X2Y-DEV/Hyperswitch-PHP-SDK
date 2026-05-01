<?php
/**
 * Refund a successful payment.
 *
 * Run from the repo root, against an existing succeeded payment id:
 *
 *     HYPERSWITCH_SANDBOX_KEY=snd_... \
 *     HYPERSWITCH_PAYMENT_ID=pay_xxx \
 *     php docs/examples/refund-create.php
 */

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use X2Y\HyperSwitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperSwitchClient\Dto\Refund\Request\CreateRefundRequest;
use X2Y\HyperSwitchClient\Enum\RefundType;
use X2Y\HyperSwitchClient\Exception\NotFoundException;
use X2Y\HyperSwitchClient\Exception\ValidationException;
use X2Y\HyperSwitchClient\HyperSwitchClient;

$apiKey    = getenv('HYPERSWITCH_SANDBOX_KEY') ?: throw new RuntimeException('Set HYPERSWITCH_SANDBOX_KEY.');
$paymentId = getenv('HYPERSWITCH_PAYMENT_ID') ?: throw new RuntimeException('Set HYPERSWITCH_PAYMENT_ID to a succeeded payment.');

$psr17  = new Psr17Factory();
$client = HyperSwitchClient::sandbox(
    httpClient:     new Psr18Client(),
    requestFactory: $psr17,
    streamFactory:  $psr17,
    auth:           [new MerchantKeyAuthResolver($apiKey)],
);

try {
    $refund = $client->refunds()->create(new CreateRefundRequest(
        paymentId:  $paymentId,
        amount:     100,                   // partial refund — 100 cents
        refundType: RefundType::INSTANT,
        reason:     'Demonstration via sdk example',
        metadata:   ['origin' => 'sdk_example'],
    ));

    echo "refund_id  : {$refund->refundId}\n";
    echo "status     : {$refund->status->value}";
    echo $refund->status->isTerminal() ? " (terminal)\n" : " (in flight)\n";
    echo "amount     : {$refund->amount} {$refund->currency->value}\n";
    echo "connector  : " . ($refund->connector ?? 'n/a') . "\n";
} catch (NotFoundException $e) {
    fwrite(STDERR, "Payment {$paymentId} not found in this merchant's records.\n");
    exit(1);
} catch (ValidationException $e) {
    // Common case: trying to refund a non-succeeded payment.
    fwrite(STDERR, "Refund rejected: {$e->getMessage()}\n");
    exit(1);
}
