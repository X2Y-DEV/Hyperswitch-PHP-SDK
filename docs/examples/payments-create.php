<?php
/**
 * End-to-end example: create a payment in the Hyperswitch sandbox.
 *
 * Run from the repo root:
 *
 *     HYPERSWITCH_SANDBOX_KEY=snd_... php docs/examples/payments-create.php
 *
 * This file is not part of the production codebase — it lives in docs/ as
 * documentation and is excluded from autoloading.
 */

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use X2Y\HyperswitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperswitchClient\Dto\Payment\Request\CreatePaymentRequest;
use X2Y\HyperswitchClient\Enum\Currency;
use X2Y\HyperswitchClient\HyperswitchClient;
use X2Y\HyperswitchClient\Transport\Middleware\IdempotencyKeyMiddleware;
use X2Y\HyperswitchClient\Transport\Middleware\RetryMiddleware;

$apiKey = getenv('HYPERSWITCH_SANDBOX_KEY') ?: throw new RuntimeException(
    'Set HYPERSWITCH_SANDBOX_KEY before running this example.'
);

$psr17 = new Psr17Factory();

$client = HyperswitchClient::sandbox(
    httpClient:     new Psr18Client(),
    requestFactory: $psr17,
    streamFactory:  $psr17,
    auth:           [new MerchantKeyAuthResolver($apiKey)],
    middleware:     [
        new IdempotencyKeyMiddleware(),
        new RetryMiddleware(maxAttempts: 3),
    ],
);

$response = $client->payments()->create(new CreatePaymentRequest(
    amount:      4500,                  // $45.00 in cents
    currency:    Currency::USD,
    description: 'SDK example payment',
));

echo "payment_id:    {$response->paymentId}\n";
echo "status:        {$response->status->value}\n";
echo "client_secret: {$response->clientSecret}\n";

// Round-trip retrieve
$fresh = $client->payments()->retrieve($response->paymentId);
echo "retrieved status: {$fresh->status->value}\n";
